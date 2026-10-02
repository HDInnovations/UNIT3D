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

namespace App\Console\Commands\Capacity;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Verifies the harness's dedicated canary peer (seeded by
 * `capacity:seed-fixtures`, never shared with the general swarm pool, and
 * driven by the single-VU `canaryAnnounce` scenario in
 * scripts/capacity/k6/announce.js) was credited for exactly what the load
 * generator sent -- no lost credit, no double-counted credit -- across a
 * full scenario run.
 *
 * `peer_announce_cursors` alone cannot prove this: it only ever stores the
 * last-reported raw byte counters, so a job that silently double-applied or
 * dropped a credit would leave the cursor looking identical. The
 * authoritative ledger is `users.uploaded`/`users.downloaded` (what
 * `ProcessAnnounce` actually adds, once per accepted announce, with its
 * `started`-event-zero-delta rule already reflected in the expected totals
 * the k6 script computed) -- that is what this command asserts against.
 * The cursor is still checked as a secondary sanity signal.
 *
 * Reads a JSON file written by k6's `handleSummary` (see
 * scripts/capacity/k6/announce.js) containing the expected final raw cursor
 * totals and the expected *credited* uploaded/downloaded for the canary
 * peer.
 */
final class VerifyCapacityCanary extends Command
{
    /** @var string */
    protected $signature = 'capacity:verify-canary
        {expected-file : Path to the JSON file written by the k6 canary scenario (expected totals)}';

    /** @var string */
    protected $description = 'Verify the canary peer was credited for exactly what the load generator sent (no loss, no double-count)';

    public function handle(): int
    {
        if (config('app.env') !== 'capacity') {
            $this->components->error('Refusing to run outside the isolated `capacity` environment (APP_ENV='.config('app.env').').');

            return self::FAILURE;
        }

        $path = $this->argument('expected-file');

        if (! is_file($path)) {
            throw new RuntimeException("Expected-totals file not found: {$path}");
        }

        $expected = json_decode((string) file_get_contents($path), true, flags: \JSON_THROW_ON_ERROR);

        foreach ([
            'user_id', 'torrent_id', 'peer_id_hex',
            'expected_cursor_uploaded', 'expected_cursor_downloaded', 'expected_cursor_left',
            'expected_credited_uploaded', 'expected_credited_downloaded', 'announce_count',
        ] as $field) {
            if (! \array_key_exists($field, $expected)) {
                throw new RuntimeException("Missing required field `{$field}` in {$path}");
            }
        }

        $failed = false;
        $failedJobs = DB::table('failed_jobs')->where('queue', 'tracker')->count();
        $pendingOutboxes = DB::table('peer_announce_cursors')->whereNotNull('pending_job_uuid')->count();
        $this->components->twoColumnDetail('Failed tracker jobs / pending outboxes', $failedJobs.' / '.$pendingOutboxes);
        if ($failedJobs !== 0 || $pendingOutboxes !== 0 || (int) $expected['announce_count'] < 1) {
            $this->components->error('The tracker pipeline lost jobs, has stalled delivery, or accepted no canary announce.');
            $failed = true;
        }

        // --- Primary assertion: credited user ledger ------------------------
        $user = DB::table('users')->where('id', '=', $expected['user_id'])->first();

        if ($user === null) {
            $this->components->error('Canary user not found — the harness fixtures are missing or were wiped mid-run.');

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('Canary announces sent', (string) $expected['announce_count']);
        $this->components->twoColumnDetail('Credited uploaded (expected/actual)', $expected['expected_credited_uploaded'].' / '.$user->uploaded);
        $this->components->twoColumnDetail('Credited downloaded (expected/actual)', $expected['expected_credited_downloaded'].' / '.$user->downloaded);

        if ((int) $user->uploaded !== (int) $expected['expected_credited_uploaded']) {
            $this->components->error(\sprintf(
                'Credited uploaded mismatch (lost or double-counted credit): expected %d, got %d',
                $expected['expected_credited_uploaded'],
                $user->uploaded,
            ));
            $failed = true;
        }

        if ((int) $user->downloaded !== (int) $expected['expected_credited_downloaded']) {
            $this->components->error(\sprintf(
                'Credited downloaded mismatch (lost or double-counted credit): expected %d, got %d',
                $expected['expected_credited_downloaded'],
                $user->downloaded,
            ));
            $failed = true;
        }

        // --- Secondary sanity: `history` is a SEPARATE pipeline from the
        // synchronous user-credit write above (delivered async via the
        // Redis `histories:batch` list and applied by `auto:upsert_histories`
        // with its own `announce_job_receipts` dedup guard), so it can catch
        // a failure mode the `users` check above cannot: correct synchronous
        // credit but a lost/duplicated async batch delivery. Same expected
        // values (both derive from the same credited deltas at the moment
        // `ProcessAnnounce` computed them). -----------------------------
        $history = DB::table('history')
            ->where('user_id', '=', $expected['user_id'])
            ->where('torrent_id', '=', $expected['torrent_id'])
            ->first();

        if ($history === null) {
            if ((int) $expected['announce_count'] > 1) {
                $this->components->error('No `history` row found for the canary (user_id, torrent_id) — the async batch flush never delivered it.');
                $failed = true;
            }
            // Exactly 1 announce ever sent means every real delta was 0
            // (the forced-zero `started` event), so a `history` row may
            // legitimately not exist yet; not a failure on its own.
        } else {
            $this->components->twoColumnDetail('History uploaded (expected/actual)', $expected['expected_credited_uploaded'].' / '.$history->uploaded);
            $this->components->twoColumnDetail('History downloaded (expected/actual)', $expected['expected_credited_downloaded'].' / '.$history->downloaded);

            if ((int) $history->uploaded !== (int) $expected['expected_credited_uploaded']
                || (int) $history->downloaded !== (int) $expected['expected_credited_downloaded']
            ) {
                $this->components->error('`history` credited totals do not match the expected totals (lost or double-counted in the async batch pipeline).');
                $failed = true;
            }
        }

        // --- Secondary sanity: raw cursor baseline matches what was sent ----
        $peerIdRaw = hex2bin($expected['peer_id_hex']);

        $cursor = DB::table('peer_announce_cursors')
            ->where('user_id', '=', $expected['user_id'])
            ->where('torrent_id', '=', $expected['torrent_id'])
            ->where('peer_id', '=', $peerIdRaw)
            ->first();

        if ($cursor === null) {
            $this->components->error('No peer_announce_cursors row found for the canary peer — every announce was lost.');

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('Cursor uploaded (expected/actual)', $expected['expected_cursor_uploaded'].' / '.$cursor->uploaded);
        $this->components->twoColumnDetail('Cursor downloaded (expected/actual)', $expected['expected_cursor_downloaded'].' / '.$cursor->downloaded);

        if ((int) $cursor->uploaded !== (int) $expected['expected_cursor_uploaded']
            || (int) $cursor->downloaded !== (int) $expected['expected_cursor_downloaded']
            || (int) $cursor->left !== (int) $expected['expected_cursor_left']
        ) {
            $this->components->error('Cursor baseline does not match the raw totals the load generator sent for the canary peer.');
            $failed = true;
        }

        // --- Stalled-delivery outbox: a real finding, not a soft warning ----
        if ($cursor->pending_job_uuid !== null) {
            $this->components->error(
                "Canary row still has a pending outbox entry (pending_job_uuid={$cursor->pending_job_uuid}): "
                .'Redis delivery stalled for its last announce and was never redelivered before verification ran. '
                .'Drain the tracker queue/batches longer before verifying.'
            );
            $failed = true;
        }

        if ($failed) {
            return self::FAILURE;
        }

        $this->components->info('Canary credited totals and cursor baseline exactly match every byte the load generator sent. No lost or double-counted credit.');

        return self::SUCCESS;
    }
}
