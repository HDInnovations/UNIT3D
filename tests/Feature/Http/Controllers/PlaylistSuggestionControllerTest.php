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
use App\Models\Playlist;
use App\Models\PlaylistSuggestion;
use App\Models\Torrent;
use App\Models\User;

test('store returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create();
    $torrent = Torrent::factory()->create();

    $response = $this->actingAs($user)->post(route('playlists.suggestions.store', [$playlist]), [
        'torrent_url' => route('torrents.show', [$torrent]),
        'message'     => 'test',
    ])
        ->assertSessionHas('success', __('playlist.suggestion-review'));

    $this->assertDatabaseHas('playlist_suggestions', [
        'torrent_id' => $torrent->id,
        'message'    => 'test',
    ]);
});

test('update returns an ok response', function (): void {
    $playlist = Playlist::factory()
        ->for(User::factory())
        ->create();
    $suggestion = PlaylistSuggestion::factory()->for($playlist)->create();

    $response = $this->actingAs($playlist->user)
        ->patch(route('playlists.suggestions.update', [$playlist, $suggestion]), [
            'status' => ModerationStatus::APPROVED->value,
        ])
        ->assertRedirect(route('playlists.show', [$playlist]).'#playlist_suggestions')
        ->assertSessionHas('success', __('playlist.suggestion-approved'));

    $this->assertDatabaseHas('playlist_torrents', [
        'playlist_id' => $playlist->id,
        'torrent_id'  => $suggestion->torrent_id,
    ]);
});
