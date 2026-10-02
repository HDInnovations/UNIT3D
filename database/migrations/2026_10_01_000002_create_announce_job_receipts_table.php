<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * `announce_job_receipts` is a short-lived, self-pruning idempotency guard
     * used by `auto:upsert_peers`, `auto:upsert_histories` and
     * `auto:upsert_announces`. Each buffered Redis batch row carries a
     * `_job_uuid` identifying the `ProcessAnnounce` job attempt that produced
     * it (see `peer_announce_cursors.pending_job_uuid`, whose redelivery on
     * retry is the other half of this guard). Before applying a chunk, a
     * command records the job_uuid/queue pairs it is about to write in the
     * SAME database transaction as the upsert/insert, and skips any row whose
     * job_uuid+queue is already present.
     *
     * `acknowledged_at` guards the OTHER replay direction (the batch command
     * itself crashing after its DB commit but before its `LTRIM`, which
     * leaves the same unconsumed Redis range for the next run to legitimately
     * reprocess): a command only stamps `acknowledged_at = NOW()` on a chunk's
     * receipts once that chunk's `LTRIM` has actually succeeded, i.e. once
     * Redis has truly forgotten those rows for good. A receipt with
     * `acknowledged_at IS NULL` must never be pruned, no matter its age --
     * that state means its payload may still be sitting unconsumed in Redis
     * and the dedup check must keep rejecting a reprocessed duplicate of it.
     *
     * Pruning is therefore safe, not time-guessed: a row is only ever deleted
     * once it is BOTH acknowledged and older than one hour past that
     * acknowledgement AND its `job_uuid` is no longer referenced by any
     * `peer_announce_cursors.pending_job_uuid` (`DELETE ... WHERE
     * acknowledged_at IS NOT NULL AND acknowledged_at < NOW() - INTERVAL 1
     * HOUR AND NOT EXISTS (SELECT 1 FROM peer_announce_cursors WHERE
     * pending_job_uuid = job_uuid)`). A still-pending `job_uuid` means
     * `ProcessAnnounce` has not yet confirmed delivery and could still
     * redeliver that exact payload, so its receipt must never be dropped
     * regardless of age either. There is no unbounded correctness gap here
     * even under an arbitrarily long Redis/DB outage, only unbounded *table
     * growth* for as long as that single outage lasts, which is an
     * acceptable, self-correcting trade (growth stops the moment delivery
     * resumes and rows become acknowledgeable/unreferenced again).
     */
    public function up(): void
    {
        Schema::create('announce_job_receipts', function (Blueprint $table): void {
            $table->uuid('job_uuid');
            $table->string('queue', 32);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();

            $table->primary(['job_uuid', 'queue']);
            $table->index('acknowledged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announce_job_receipts');
    }
};
