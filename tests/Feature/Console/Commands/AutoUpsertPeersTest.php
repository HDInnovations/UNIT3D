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

use App\Enums\ModerationStatus;
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

function bufferPeer(int $userId, int $torrentId, string $peerId, int $port): void
{
    Redis::connection('announce')->command('RPUSH', [
        config('cache.prefix').':peers:batch',
        serialize([
            'peer_id'     => $peerId,
            'ip'          => inet_pton('10.0.0.40'),
            'port'        => $port,
            'agent'       => 'qBittorrent/5.2.4',
            'uploaded'    => 0,
            'downloaded'  => 0,
            'left'        => 0,
            'seeder'      => true,
            'torrent_id'  => $torrentId,
            'user_id'     => $userId,
            'active'      => true,
            'visible'     => true,
            'connectable' => false,
            '_job_uuid'   => (string) Str::uuid(),
        ]),
    ]);
}

test('buffered peers are written to the database', function (): void {
    Redis::connection('announce')->flushdb();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    bufferPeer($user->id, $torrent->id, '19045931013802080695', 34239);

    $this->artisan('auto:upsert_peers')->assertSuccessful();

    expect(Peer::query()->where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->value('port'))
        ->toBe(34239)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
        ->toBe(0);
});

test('a buffered peer of a deleted user does not block the remaining peers', function (): void {
    Redis::connection('announce')->flushdb();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    bufferPeer($user->id + 1000, $torrent->id, '19045931013802080694', 6881);
    bufferPeer($user->id, $torrent->id + 1000, '19045931013802080693', 6882);
    bufferPeer($user->id, $torrent->id, '19045931013802080695', 34239);

    $this->artisan('auto:upsert_peers')->assertSuccessful();

    expect(Peer::query()->count())
        ->toBe(1)
        ->and(Peer::query()->where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->value('port'))
        ->toBe(34239)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
        ->toBe(0);
});

test('a peer appended to the batch while a chunk is mid-flush is not dropped by the trim', function (): void {
    Redis::connection('announce')->flushdb();
    DB::table('announce_job_receipts')->truncate();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    bufferPeer($user->id, $torrent->id, '19045931013802080695', 1111);

    // Simulate a second, concurrent `ProcessAnnounce` RPUSHing a fresh row
    // onto the exact same batch key while this flush's DB write is still in
    // flight -- before it has trimmed anything. A trim that assumed the full
    // per-cycle chunk size was read (instead of what was actually read)
    // would blow straight past this row and delete it too. `DB::listen`
    // (not `DB::afterCommit`) is used deliberately: tests run inside an
    // outer `LazilyRefreshDatabase` transaction, so the command's own
    // internal transaction is only a nested savepoint and never fires a real
    // commit event during the test.
    $fired = false;
    DB::listen(function ($query) use ($user, $torrent, &$fired): void {
        if (!$fired && str_contains($query->sql, '`peers`')) {
            $fired = true;
            bufferPeer($user->id, $torrent->id, '19045931013802080696', 2222);
        }
    });

    $this->artisan('auto:upsert_peers')->assertSuccessful();

    expect(Peer::query()->count())->toBe(1)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
        ->toBe(1);

    $this->artisan('auto:upsert_peers')->assertSuccessful();

    expect(Peer::query()->count())->toBe(2)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
        ->toBe(0);
});

test('a concurrently running flush of the same queue is skipped, leaving the batch untouched', function (): void {
    Redis::connection('announce')->flushdb();
    DB::table('announce_job_receipts')->truncate();

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['status' => ModerationStatus::APPROVED]);

    bufferPeer($user->id, $torrent->id, '19045931013802080695', 1111);

    // Hold the exact named lock `withFlushSingleFlight('peers', ...)` uses,
    // on a second, independent DB connection -- simulating another process
    // (a manual run, a second scheduler host) already flushing this queue.
    $config = config('database.connections.mysql');
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
        $config['username'],
        $config['password'],
    );
    // Must match FiltersOrphanedAnnounceRows::withFlushSingleFlight()'s naming.
    $lockName = 'announce_flush:'.hash('crc32b', config('cache.prefix').':peers');

    $pdo->query("SELECT GET_LOCK('{$lockName}', 0)")->fetch();

    try {
        $this->artisan('auto:upsert_peers')->assertSuccessful();

        // Could not acquire the lock, so nothing was read/trimmed/upserted.
        expect(Peer::query()->count())->toBe(0)
            ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
            ->toBe(1);
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('{$lockName}')")->fetch();
    }

    // Lock released: a normal run now proceeds.
    $this->artisan('auto:upsert_peers')->assertSuccessful();

    expect(Peer::query()->count())->toBe(1)
        ->and(Redis::connection('announce')->command('LLEN', [config('cache.prefix').':peers:batch']))
        ->toBe(0);
});
