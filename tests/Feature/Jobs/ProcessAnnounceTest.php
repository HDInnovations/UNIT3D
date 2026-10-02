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

use App\DTO\AnnounceGroupDTO;
use App\DTO\AnnounceQueryDTO;
use App\DTO\AnnounceTorrentDTO;
use App\DTO\AnnounceUserDTO;
use App\Enums\ModerationStatus;
use App\Jobs\ProcessAnnounce;
use App\Models\History;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

function makeAnnounceJob(User $user, Torrent $torrent, string $peerId, int $uploaded, int $downloaded, int $left, string $event = ''): ProcessAnnounce
{
    $queries = new AnnounceQueryDTO(
        port: 6881,
        uploaded: $uploaded,
        downloaded: $downloaded,
        left: $left,
        corrupt: 0,
        numwant: 50,
        event: $event,
        key: 'testkey',
        agent: 'qBittorrent/5.2.4',
        infoHash: str_pad('h', 20, '0'),
        peerId: str_pad($peerId, 20, '0'),
        ip: inet_pton('10.0.0.1'),
    );

    return new ProcessAnnounce(
        $queries,
        new AnnounceUserDTO($user->id, false, new AnnounceGroupDTO(false, false, false)),
        new AnnounceTorrentDTO($torrent->id, 0, false),
        true,
        now(),
    );
}

/**
 * Make `Redis::connection('announce')->command('RPUSH', [$peersBatchKey, ...])`
 * throw on its very first invocation (simulating a transient Redis outage
 * hitting the delivery step after the DB transaction already committed),
 * then transparently fall through to the real connection for everything
 * else -- including the later retry's own RPUSH calls and any
 * `auto:upsert_*` flush run in the same test. This fails the real Redis
 * call a job makes, rather than mocking any of `ProcessAnnounce`'s internals.
 */
function failFirstPeerRpush(): void
{
    $realConnection = Redis::connection('announce');
    $hasThrown = false;

    $proxy = Mockery::mock($realConnection)->makePartial();
    $proxy->shouldReceive('command')->andReturnUsing(
        function (string $command, array $args) use ($realConnection, &$hasThrown) {
            if (!$hasThrown && $command === 'RPUSH' && $args[0] === config('cache.prefix').':peers:batch') {
                $hasThrown = true;

                throw new RuntimeException('simulated redis outage');
            }

            return $realConnection->command($command, $args);
        },
    );

    Redis::shouldReceive('connection')->with('announce')->andReturn($proxy);
}

beforeEach(function (): void {
    Redis::connection('announce')->flushdb();
});

test('a retried job after a delivery failure redelivers the stored outbox without double-crediting', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    makeAnnounceJob($user, $torrent, 'peer-a', 0, 0, 100, 'started')->handle();
    Redis::connection('announce')->flushdb(); // drop the baseline announce's own buffered rows

    $job = makeAnnounceJob($user, $torrent, 'peer-a', 5000, 0, 100);

    failFirstPeerRpush();

    // First attempt: the DB transaction (credit + cursor advance + outbox
    // write) commits, but delivery to Redis fails.
    expect(fn () => $job->handle())->toThrow(RuntimeException::class);

    // Credit was already applied by the committed transaction.
    expect((int) $user->refresh()->uploaded)->toBe(5000);

    $cursorBeforeRetry = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)->where('torrent_id', $torrent->id)->first();
    expect($cursorBeforeRetry->pending_job_uuid)->not->toBeNull();

    // A queue retry re-executes the SAME job attempt. Redis is healthy again.
    $job->handle();

    expect((int) $user->refresh()->uploaded)->toBe(5000); // not credited a second time

    $cursor = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)->where('torrent_id', $torrent->id)->first();
    expect($cursor->pending_job_uuid)->toBeNull();

    // Consumer-side: the redelivered payload actually lands correctly, once,
    // in the real tables -- not just "something is in a Redis list".
    $this->artisan('auto:upsert_peers')->assertSuccessful();
    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect(Peer::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->count())->toBe(1)
        ->and((int) History::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->value('uploaded'))
        ->toBe(5000);
});

test('two announces for the same peer processed back-to-back credit correctly without double counting', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    makeAnnounceJob($user, $torrent, 'peer-b', 0, 0, 100, 'started')->handle();

    // Two real announces land before any batch flush has a chance to run
    // (the old bug: both would have read the same stale baseline).
    makeAnnounceJob($user, $torrent, 'peer-b', 1000, 0, 100)->handle();
    makeAnnounceJob($user, $torrent, 'peer-b', 2500, 0, 100)->handle();

    expect((int) $user->refresh()->uploaded)->toBe(2500);

    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect((int) History::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->value('uploaded'))
        ->toBe(2500);
});

test('a stale job that executes after a newer job already advanced the baseline is a no-op', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    makeAnnounceJob($user, $torrent, 'peer-c', 0, 0, 100, 'started')->handle();

    $older = makeAnnounceJob($user, $torrent, 'peer-c', 1000, 0, 100);
    $newer = makeAnnounceJob($user, $torrent, 'peer-c', 3000, 0, 100);

    // Force the "older" job to have an earlier received_at than the "newer"
    // one even though it executes second (simulating a retried/delayed job
    // landing after a fresh announce already committed).
    $older->receivedAt = now()->subMinute();

    $newer->handle();
    expect((int) $user->refresh()->uploaded)->toBe(3000);

    $older->handle();

    // The stale job must not re-credit and must not regress the baseline.
    expect((int) $user->refresh()->uploaded)->toBe(3000);

    $cursor = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)
        ->where('torrent_id', $torrent->id)
        ->first();

    expect((int) $cursor->uploaded)->toBe(3000);

    // A genuinely new, later announce still works normally afterwards.
    makeAnnounceJob($user, $torrent, 'peer-c', 4000, 0, 100)->handle();
    expect((int) $user->refresh()->uploaded)->toBe(4000);
});

test('jobs received within the same wall-clock second are still ordered by microsecond precision', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    $second = now()->startOfSecond();
    $baseline = makeAnnounceJob($user, $torrent, 'peer-e', 0, 0, 100, 'started');
    $baseline->receivedAt = $second->copy()->subSecond();
    $baseline->handle();

    $older = makeAnnounceJob($user, $torrent, 'peer-e', 1000, 0, 100);
    $older->receivedAt = $second->copy()->addMicroseconds(100);

    $newer = makeAnnounceJob($user, $torrent, 'peer-e', 3000, 0, 100);
    $newer->receivedAt = $second->copy()->addMicroseconds(900);

    // Same second, different microsecond -- `received_at` must retain that
    // precision end to end (DB column, write, and comparison) or these two
    // would compare equal and the ordering guard would be a no-op.
    $newer->handle();
    expect((int) $user->refresh()->uploaded)->toBe(3000);

    $older->handle();

    expect((int) $user->refresh()->uploaded)->toBe(3000);

    $cursor = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)
        ->where('torrent_id', $torrent->id)
        ->first();

    expect((int) $cursor->uploaded)->toBe(3000);
});

test('a genuine client counter reset is honoured when it is the latest announce for that peer', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    makeAnnounceJob($user, $torrent, 'peer-f', 0, 0, 100, 'started')->handle();
    makeAnnounceJob($user, $torrent, 'peer-f', 5000, 0, 100)->handle();

    expect((int) $user->refresh()->uploaded)->toBe(5000);

    // Same peer_id, but the client restarted and is now reporting a lower
    // cumulative counter than before. This is the genuinely latest announce
    // (not stale/out-of-order), so it must be honoured, not floored/ignored:
    // the baseline drops to the new value and no negative credit is applied.
    makeAnnounceJob($user, $torrent, 'peer-f', 100, 0, 100)->handle();

    expect((int) $user->refresh()->uploaded)->toBe(5000); // unchanged, no negative credit

    $cursor = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)
        ->where('torrent_id', $torrent->id)
        ->first();

    expect((int) $cursor->uploaded)->toBe(100);

    // The next real announce now credits correctly against the new, lower baseline.
    makeAnnounceJob($user, $torrent, 'peer-f', 250, 0, 100)->handle();
    expect((int) $user->refresh()->uploaded)->toBe(5150);
});

test('a stalled outbox payload from a crashed job is drained (not lost) by the next announce for the same peer', function (): void {
    $user = User::factory()->create(['uploaded' => 0]);
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    makeAnnounceJob($user, $torrent, 'peer-d', 0, 0, 100, 'started')->handle();
    Redis::connection('announce')->flushdb(); // drop the baseline announce's own buffered rows

    $stuck = makeAnnounceJob($user, $torrent, 'peer-d', 1500, 0, 100);

    failFirstPeerRpush();

    // Commits credit + outbox, then "crashes" (delivery never happens and is
    // never retried for this exact job again -- e.g. it exhausted its tries).
    expect(fn () => $stuck->handle())->toThrow(RuntimeException::class);

    expect((int) $user->refresh()->uploaded)->toBe(1500);

    // A later, different announce for the same peer must drain the stuck
    // outbox before doing its own work, not silently overwrite/lose it.
    makeAnnounceJob($user, $torrent, 'peer-d', 2000, 0, 100)->handle();

    expect((int) $user->refresh()->uploaded)->toBe(2000);

    $cursor = DB::table('peer_announce_cursors')
        ->where('user_id', $user->id)
        ->where('torrent_id', $torrent->id)
        ->first();

    expect($cursor->pending_job_uuid)->toBeNull();

    // Consumer-side: both the drained (1500) and the new (500) deltas
    // actually reached the History row -- the stuck payload was not lost.
    $this->artisan('auto:upsert_histories')->assertSuccessful();

    expect((int) History::query()->where('user_id', $user->id)->where('torrent_id', $torrent->id)->value('uploaded'))
        ->toBe(2000);
});
