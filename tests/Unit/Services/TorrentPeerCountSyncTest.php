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

use App\Models\Peer;
use App\Models\Torrent;
use App\Models\User;
use App\Services\TorrentPeerCountSync;

function peerOn(Torrent $torrent, string $peerId, int $left, bool $active = true, bool $visible = true): void
{
    Peer::factory()->create([
        'torrent_id' => $torrent->id,
        'user_id'    => User::factory(),
        'peer_id'    => str_pad($peerId, 20, '-'),
        'left'       => $left,
        'seeder'     => $left === 0,
        'active'     => $active,
        'visible'    => $visible,
    ]);
}

test('counts only active visible peers and reports the torrents it changed', function (): void {
    $torrent = Torrent::factory()->create(['seeders' => 0, 'leechers' => 0]);

    peerOn($torrent, 'seed-1', 0);
    peerOn($torrent, 'seed-2', 0);
    peerOn($torrent, 'leech-1', 500);
    peerOn($torrent, 'stopped', 0, active: false);
    peerOn($torrent, 'hidden', 500, visible: false);

    $sync = new TorrentPeerCountSync();

    expect($sync->sync([$torrent->id]))->toBe([$torrent->id])
        ->and($torrent->fresh()->only(['seeders', 'leechers']))->toBe(['seeders' => 2, 'leechers' => 1])
        ->and($sync->sync([$torrent->id]))->toBe([]);
});

test('a scoped sync leaves torrents outside the batch untouched', function (): void {
    $announced = Torrent::factory()->create(['seeders' => 0, 'leechers' => 0]);
    $other = Torrent::factory()->create(['seeders' => 7, 'leechers' => 3]);

    peerOn($announced, 'seed-1', 0);

    expect((new TorrentPeerCountSync())->sync([$announced->id]))->toBe([$announced->id])
        ->and($other->fresh()->only(['seeders', 'leechers']))->toBe(['seeders' => 7, 'leechers' => 3]);
});

test('a full sync resets counters of torrents that lost all peers', function (): void {
    $abandoned = Torrent::factory()->create(['seeders' => 4, 'leechers' => 2]);

    expect((new TorrentPeerCountSync())->sync())->toContain($abandoned->id)
        ->and($abandoned->fresh()->only(['seeders', 'leechers']))->toBe(['seeders' => 0, 'leechers' => 0]);
});
