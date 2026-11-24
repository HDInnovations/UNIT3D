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

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Http\Requests\StorePlaylistSuggestionRequest;
use App\Http\Requests\UpdatePlaylistSuggestionRequest;
use App\Models\Playlist;
use App\Models\PlaylistSuggestion;
use App\Models\PlaylistTorrent;
use App\Models\User;
use App\Notifications\PlaylistSuggestionCreated;
use App\Notifications\PlaylistSuggestionRejected;
use Illuminate\Container\Attributes\CurrentUser;

class PlaylistSuggestionController extends Controller
{
    /**
     * Store a new playlist.
     */
    public function store(StorePlaylistSuggestionRequest $request, Playlist $playlist): \Illuminate\Http\RedirectResponse
    {
        $playlistSuggestion = $playlist->suggestions()->create($request->toArray());

        $playlist->user->notify(new PlaylistSuggestionCreated($playlistSuggestion));

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.suggestion-review'));
    }

    /**
     * Update a playlist suggestion.
     */
    public function update(
        UpdatePlaylistSuggestionRequest $request,
        Playlist $playlist,
        PlaylistSuggestion $playlistSuggestion,
        #[CurrentUser] User $currentUser,
    ): \Illuminate\Http\RedirectResponse {
        abort_unless($currentUser->id == $playlist->user_id || $currentUser->group->is_modo, 403);

        switch ($request->status) {
            case ModerationStatus::APPROVED:
                $playlistTorrent = PlaylistTorrent::query()->create([
                    'playlist_id' => $playlistSuggestion->playlist_id,
                    'torrent_id'  => $playlistSuggestion->torrent_id,
                ]);

                $playlistTorrent->torrent()->searchable();
                $playlistSuggestion->delete();

                return to_route('playlists.show', ['playlist' => $playlist])
                    ->withFragment('#playlist_suggestions')
                    ->with('success', trans('playlist.suggestion-approved'));
            case ModerationStatus::REJECTED:
                $playlistSuggestion->user->notify(new PlaylistSuggestionRejected($playlistSuggestion, $request->rejectionMessage));
                $playlistSuggestion->delete();

                return to_route('playlists.show', ['playlist' => $playlist])
                    ->withFragment('#playlist_suggestions')
                    ->with('success', trans('playlist.suggestion-rejected'));

            default:
                return to_route('playlists.show', ['playlist' => $playlist]);
        }
    }
}
