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

use App\Models\Playlist;
use App\Models\PlaylistTorrent;
use App\Models\Torrent;
use App\Models\User;

test('destroy returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlistTorrent = PlaylistTorrent::factory()->create([
        'playlist_id' => Playlist::factory()->create([
            'user_id' => $user->id, // create a playlist with the same user
        ]),
    ]);

    $response = $this->actingAs($user)->delete(route('playlist_torrents.destroy', [$playlistTorrent]));
    $response->assertRedirect(route('playlists.show', ['playlist' => $playlistTorrent->playlist]))->assertSessionHas('success', trans('playlist.detached-success'));

    $this->assertModelMissing($playlistTorrent);
});

test('destroy aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlistTorrent = PlaylistTorrent::factory()->create([
        'playlist_id' => Playlist::factory()->create([
            'user_id' => User::factory()->create()->id, // create a playlist with a different user
        ]),
    ]);

    $response = $this->actingAs($user)->delete(route('playlist_torrents.destroy', [$playlistTorrent]));
    $response->assertForbidden();
});

test('mass upsert returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);
    Torrent::factory()->create([
        'id' => 1,
    ]); // create a torrent with id 1

    $response = $this->actingAs($user)->put(route('playlist_torrents.massUpsert'), [
        'playlist_id'  => $playlist->id,
        'torrent_urls' => config('app.url').'/torrents/1'
    ]);
    $response->assertRedirect(route('playlists.show', ['playlist' => $playlist]));
    $response->assertSessionHas('success', trans('playlist.attached-success'));
});

test('mass upsert aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => User::factory()->create()->id, // create a playlist with a different user
    ]);

    $response = $this->actingAs($user)->put(route('playlist_torrents.massUpsert'), [
        'playlist_id'  => $playlist->id,
        'torrent_urls' => config('app.url').'/torrents/1'
    ]);
    $response->assertForbidden();
});

test('store returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);

    $response = $this->actingAs($user)->post(route('playlist_torrents.store'), [
        'playlist_id' => $playlist->id,
        'torrent_id'  => Torrent::factory()->create()->id,
    ]);
    $response->assertRedirect(route('playlists.show', ['playlist' => $playlist]));
    $response->assertSessionHas('success', trans('playlist.attached-success'));
});

test('store aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => User::factory()->create()->id, // create a playlist with a different user
    ]);

    $response = $this->actingAs($user)->post(route('playlist_torrents.store'), [
        'playlist_id' => $playlist->id,
        'torrent_id'  => Torrent::factory()->create()->id,
    ]);
    $response->assertForbidden();
});
