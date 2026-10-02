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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Traits;

use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Closure;

trait FiltersOrphanedAnnounceRows
{
    /**
     * Drop buffered announce rows whose user or torrent no longer exists.
     *
     * Announces are buffered in redis and flushed later, so a user or torrent
     * deleted in between leaves rows that violate the foreign keys of `peers`,
     * `histories` and `announces`. Since the whole batch is written in a single
     * statement, one such row aborts every flush and stalls all peer bookkeeping
     * until it is removed by hand.
     *
     * @param  array<int, array{user_id: int, torrent_id: int, ...}> $rows
     * @return array<int, array{user_id: int, torrent_id: int, ...}>
     */
    private function withoutOrphanedRows(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $userIds = User::query()
            ->whereIntegerInRaw('id', array_unique(array_column($rows, 'user_id')))
            ->pluck('id')
            ->flip();

        $torrentIds = Torrent::query()
            ->withoutGlobalScopes()
            ->whereIntegerInRaw('id', array_unique(array_column($rows, 'torrent_id')))
            ->pluck('id')
            ->flip();

        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => $userIds->has($row['user_id']) && $torrentIds->has($row['torrent_id']),
        ));
    }

    /**
     * Drop buffered rows already applied under the same `ProcessAnnounce` job
     * attempt (`_job_uuid`).
     *
     * A row can be seen twice by a flush: `App\Jobs\ProcessAnnounce` redelivers
     * its stored outbox payload verbatim whenever the same job attempt retries
     * after it already committed its DB-side effects (see
     * `peer_announce_cursors.pending_job_uuid`), and a crash between this
     * command's own DB commit and its `LTRIM` would otherwise cause the next
     * run to reprocess the same unconsumed Redis range. Both would silently
     * double-apply additive `history` deltas or insert duplicate `announces`
     * rows without this guard.
     *
     * Also collapses duplicate `_job_uuid`s found *within* `$rows` itself
     * (keeping the first occurrence): a producer redelivery landing in the
     * same Redis range as an earlier, not-yet-trimmed copy means a single
     * `LRANGE` can see the same job_uuid twice before either has ever been
     * recorded as a receipt, so checking only against already-committed
     * receipts is not enough.
     *
     * @param  array<int, array{_job_uuid: string, ...}> $rows
     * @return array<int, array{_job_uuid: string, ...}>
     */
    private function withoutDuplicateJobs(string $queue, array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $alreadyProcessed = DB::table('announce_job_receipts')
            ->where('queue', '=', $queue)
            ->whereIn('job_uuid', array_unique(array_column($rows, '_job_uuid')))
            ->pluck('job_uuid')
            ->flip();

        $seenInThisChunk = [];

        return array_values(array_filter(
            $rows,
            static function (array $row) use ($alreadyProcessed, &$seenInThisChunk): bool {
                $uuid = $row['_job_uuid'];

                if ($alreadyProcessed->has($uuid) || isset($seenInThisChunk[$uuid])) {
                    return false;
                }

                $seenInThisChunk[$uuid] = true;

                return true;
            },
        ));
    }

    /**
     * Record that the given rows' job attempts have been applied to `$queue`,
     * and strip the bookkeeping `_job_uuid` key before the rows are written to
     * their destination table.
     *
     * Must be called inside the same transaction as the write it guards.
     *
     * @param  array<int, array{_job_uuid: string, ...}> $rows
     * @return array<int, array<string, mixed>>
     */
    private function recordJobReceiptsAndStripKey(string $queue, array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        DB::table('announce_job_receipts')->insertOrIgnore(array_map(
            static fn (array $row): array => [
                'job_uuid'   => $row['_job_uuid'],
                'queue'      => $queue,
                'created_at' => now(),
            ],
            $rows,
        ));

        return array_map(
            static fn (array $row): array => array_diff_key($row, ['_job_uuid' => null]),
            $rows,
        );
    }

    /**
     * Mark this chunk's receipts as acknowledged once -- and only once -- the
     * `LTRIM` that actually removed them from the Redis batch list has
     * succeeded. Must be called for every `_job_uuid` in the flushed chunk,
     * including ones that were skipped as duplicates: a duplicate's Redis
     * copy is being trimmed away right now too, so its receipt becomes
     * prunable from this point on as well.
     *
     * @param array<int, string> $jobUuids
     */
    private function acknowledgeJobReceipts(string $queue, array $jobUuids): void
    {
        if ($jobUuids === []) {
            return;
        }

        DB::table('announce_job_receipts')
            ->where('queue', '=', $queue)
            ->whereIn('job_uuid', array_unique($jobUuids))
            ->update(['acknowledged_at' => now()]);
    }

    /**
     * Run `$callback` only if no other process (a manually invoked command, a
     * second overlapping scheduler tick, a second scheduler host, etc.) is
     * already flushing the same `$queue`. `WithoutOverlapping` on the
     * dispatching job only serializes `ProcessAnnounce` attempts against each
     * other -- it says nothing about two instances of this flush command
     * itself running concurrently, which would `LRANGE` the same range
     * twice, double-apply it, and then both `LTRIM` it (the second trim
     * silently dropping whatever the first run's `LTRIM` left behind, or
     * whatever was appended in between).
     *
     * Uses a MySQL named lock (`GET_LOCK`) held on the connection actually
     * used for the writes/receipts below, rather than an arbitrary
     * cache-based lock with its own TTL/expiry to reason about: it is
     * automatically released the moment this process's DB connection closes
     * or drops, there is nothing to time out while a (bounded, not
     * long-running) flush command is genuinely still working, and `0`
     * timeout means a second concurrent invocation returns immediately
     * instead of queueing up behind the first.
     *
     * `$callback` receives a `stillHoldsLock(): bool` check that MUST be
     * consulted immediately before every `LTRIM` of the Redis batch list: if
     * the DB connection silently reconnected mid-run (e.g. "MySQL server has
     * gone away"), the named lock is gone with it even though this process
     * doesn't know that yet. Trimming Redis without actually holding the
     * lock could race a second process that validly acquired it on the new
     * connection. Skipping the trim in that case is always safe -- the
     * `announce_job_receipts` guard means whichever process trims it next
     * will not re-apply rows this run already committed.
     *
     * The lock name is scoped by `config('cache.prefix')` (and shortened via
     * a hash) rather than just `$queue`: MySQL named locks are global to the
     * whole server, not to a single schema/database, so an unscoped name
     * could collide with an unrelated app/environment sharing the same MySQL
     * instance. `GET_LOCK`/`IS_USED_LOCK`/`CONNECTION_ID` are all forced onto
     * the write PDO (`useReadPdo: false`): session-level named locks only
     * mean anything on the exact connection that acquired them, so if a
     * read/write split were ever configured, resolving these via the read
     * PDO could silently check a different session than the one that's
     * actually holding (or releasing) the lock.
     */
    private function withFlushSingleFlight(string $queue, Closure $callback): void
    {
        $lockName = 'announce_flush:'.hash('crc32b', config('cache.prefix').':'.$queue);

        $acquired = (bool) DB::selectOne('SELECT GET_LOCK(?, 0) AS acquired', [$lockName], false)->acquired;

        if (!$acquired) {
            return;
        }

        try {
            $callback(function () use ($lockName): bool {
                $heldBy = DB::selectOne('SELECT IS_USED_LOCK(?) AS connection_id', [$lockName], false)->connection_id;
                $thisConnection = DB::selectOne('SELECT CONNECTION_ID() AS id', [], false)->id;

                return $heldBy !== null && (int) $heldBy === (int) $thisConnection;
            });
        } finally {
            DB::statement('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    /**
     * Safely prune `announce_job_receipts`.
     *
     * A row is only dropped once ALL of the following hold:
     *   - `acknowledged_at` is set (this command's own `LTRIM` actually
     *     removed the payload from Redis for good -- an unacknowledged
     *     receipt may still guard a legitimate reprocessing of a range that
     *     was committed but never trimmed because the command crashed in
     *     between, so it must never be pruned no matter its age);
     *   - it has been acknowledged for over an hour (normal flush cycles
     *     acknowledge within seconds, this is just a safety margin); and
     *   - its `job_uuid` is no longer referenced by
     *     `peer_announce_cursors.pending_job_uuid` (a still-pending uuid
     *     means `ProcessAnnounce` has not yet confirmed delivery and could
     *     still redeliver that exact payload).
     *
     * This keeps pruning correct (no dedup gap in either direction) even
     * under an arbitrarily long Redis/DB outage; the only cost is the
     * receipts table growing for as long as that single outage lasts, which
     * self-corrects the moment delivery/flushing resumes.
     */
    private function pruneJobReceipts(): void
    {
        DB::table('announce_job_receipts')
            ->whereNotNull('acknowledged_at')
            ->where('acknowledged_at', '<', now()->subHour())
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('peer_announce_cursors')
                    ->whereColumn('peer_announce_cursors.pending_job_uuid', 'announce_job_receipts.job_uuid');
            })
            ->delete();
    }
}
