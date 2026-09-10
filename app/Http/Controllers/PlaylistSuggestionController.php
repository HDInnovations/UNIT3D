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
use App\Notifications\PlaylistSuggestionCreated;
use App\Notifications\PlaylistSuggestionRejected;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlaylistSuggestionController extends Controller
{
    /**
     * Store a new playlist.
     */
    public function store(StorePlaylistSuggestionRequest $request, Playlist $playlist): \Illuminate\Http\RedirectResponse
    {
        Validator::make([
            'torrent_id' => basename($request->torrent_url)
        ], [
            'torrent_id' => [
                Rule::exists('torrents', 'id'),
                Rule::unique('playlist_suggestions')->where('playlist_id', $playlist->id),
            ],
        ], [
            'torrent_id.exists' => 'The torrent ID/URL ":input" entered was not found on site.',
            'torrent_id.unique' => 'This torrent ID/URL ":input" entered is already suggested and awaiting moderation.',
        ])->validate();

        $playlistSuggestion = PlaylistSuggestion::query()->create([
            'playlist_id' => $playlist->id,
            'torrent_id'  => basename($request->torrent_url),
            'user_id'     => $request->user()->id,
            'message'     => $request->message,
        ]);

        $playlist->user->notify(new PlaylistSuggestionCreated($playlistSuggestion));

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.suggestion-review'));
    }

    /**
     * Update a playlist suggestion.
     */
    public function update(UpdatePlaylistSuggestionRequest $request, Playlist $playlist, PlaylistSuggestion $playlistSuggestion): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->id == $playlist->user_id || $request->user()->group->is_modo, 403);

        switch (ModerationStatus::from($request->integer('status'))) {
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
                $playlistSuggestion->user->notify(new PlaylistSuggestionRejected($playlistSuggestion, $request->rejection_message));
                $playlistSuggestion->delete();

                return to_route('playlists.show', ['playlist' => $playlist])
                    ->withFragment('#playlist_suggestions')
                    ->with('success', trans('playlist.suggestion-rejected'));

            default:
                return to_route('playlists.show', ['playlist' => $playlist]);
        }
    }
}
