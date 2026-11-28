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

use App\Models\Torrent;
use App\Models\TorrentTrump;

test('destroy returns an ok response', function (): void {
    $torrentTrump = TorrentTrump::factory()->create();

    $this->delete(route('torrent.trump.destroy', [$torrentTrump->torrent]))
        ->assertRedirectToRoute('torrents.show', $torrentTrump->torrent)
        ->assertSessionHas('success', 'Torrent trump flag removed!');
    $this->assertModelMissing($torrentTrump);
});

test('store returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $torrentTrump = TorrentTrump::factory()->make(['torrent_id' => $torrent->id])->makeHidden('user_id');

    $this->post(route('torrent.trump.store', [$torrent]), $torrentTrump->toArray())
        ->assertRedirectToRoute('torrents.show', $torrent)
        ->assertSessionHas('success', 'Torrent flagged as trumpable!');

    $this->assertDatabaseHas('torrent_trumps', $torrentTrump->toArray());
});
