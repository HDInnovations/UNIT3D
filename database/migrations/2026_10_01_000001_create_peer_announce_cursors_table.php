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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * `peer_announce_cursors` is the single authoritative source of the "last
     * accounted for" uploaded/downloaded/left byte counters per (user, torrent,
     * peer_id). It is written synchronously and transactionally by
     * `App\Jobs\ProcessAnnounce` (with a row lock) so that upload/download
     * credit deltas can never be computed twice from the same stale baseline,
     * whether because a job retried after a crash or because two announces for
     * the same peer were processed back-to-back before the unrelated, batched
     * `peers` table (display/seeder-leecher data, still flushed every few
     * seconds by `auto:upsert_peers`) had a chance to catch up.
     *
     * The `pending_*` columns are a single-row transactional outbox: the exact
     * bytes a successful announce intends to RPUSH onto the
     * `peers:batch`/`histories:batch`/`announces:batch` Redis lists
     * (`base64_encode(serialize($payload))`, verbatim) are stored here, in the
     * SAME transaction that advances the cursor and applies the user credit,
     * keyed by `pending_job_uuid` (the stable UUID of the `ProcessAnnounce`
     * job attempt that produced them). If delivery to Redis fails or the
     * worker crashes after commit, a retry of the same job attempt (same
     * UUID) redelivers the stored bytes verbatim instead of recomputing
     * deltas against the now-advanced baseline, so no credit is ever lost or
     * double counted. Once delivery succeeds the columns are cleared back to
     * NULL. Base64-encoded `TEXT` (not raw `BLOB`/`JSON`) because the
     * payloads embed raw binary `peer_id`/`ip` fields that are not valid
     * UTF-8 -- `TEXT` under utf8mb4 would reject them unescaped, and base64
     * keeps the column readable with plain string tooling.
     *
     * `pending_job_uuid` is indexed: it is the authoritative signal the
     * `auto:upsert_*` flush commands use to decide whether an
     * `announce_job_receipts` row is still safe to prune (see that
     * migration) -- a receipt may never be dropped while its job_uuid is
     * still referenced here, since that means the producer has not yet
     * confirmed delivery and could still redeliver it.
     *
     * `received_at` is the wall-clock time `App\Jobs\ProcessAnnounce` was
     * constructed (dispatched), not when it happened to execute. Queue
     * retries/requeues do not preserve FIFO order, so an older announce can
     * land and commit after a newer one already has; a job only applies its
     * credit/baseline update if its own `received_at` is strictly newer than
     * the cursor's stored value, after first draining any stale pending
     * outbox. A stale job is a true no-op (no credit, no baseline change, no
     * Redis delivery) rather than silently clamped, so a legitimate client
     * reset that happens to report lower counters is still honoured as long
     * as it is genuinely the most recently received announce for this peer.
     *
     * Deliberately separate from `peers`: this table is written on every
     * single announce (not batched) and must never be overwritten by the
     * periodic peer batch flush.
     *
     * Backfilled from `peers` so existing peers are not treated as brand new
     * (which would silently drop their next credited delta) the moment this
     * table starts being consulted.
     */
    public function up(): void
    {
        Schema::create('peer_announce_cursors', function (Blueprint $table): void {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('torrent_id');
            $table->binary('peer_id', length: 20, fixed: true);
            $table->unsignedBigInteger('uploaded')->default(0);
            $table->unsignedBigInteger('downloaded')->default(0);
            $table->unsignedBigInteger('left')->default(0);
            $table->dateTime('received_at', precision: 6);
            $table->uuid('pending_job_uuid')->nullable()->index();
            $table->text('pending_peer_payload')->nullable();
            $table->text('pending_history_payload')->nullable();
            $table->text('pending_announce_payload')->nullable();
            $table->timestamps();

            $table->primary(['user_id', 'torrent_id', 'peer_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('torrent_id')->references('id')->on('torrents')->cascadeOnDelete();
        });

        // `received_at` is seeded to the epoch so that the very first real
        // announce processed for an already-existing peer (whose own
        // `received_at` is necessarily far later) is never mistaken for a
        // stale/superseded job.
        DB::statement(
            <<<'SQL'
                INSERT INTO peer_announce_cursors (user_id, torrent_id, peer_id, uploaded, downloaded, `left`, received_at, created_at, updated_at)
                SELECT user_id, torrent_id, peer_id, uploaded, downloaded, `left`, '1970-01-01 00:00:00', NOW(), NOW()
                FROM peers
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_announce_cursors');
    }
};
