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
use Illuminate\Support\Facades\Redis;

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
