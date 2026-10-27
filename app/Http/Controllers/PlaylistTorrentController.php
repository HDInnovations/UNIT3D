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

namespace App\Http\Controllers;

use App\Http\Requests\MassUpsertPlaylistTorrentRequest;
use App\Http\Requests\StorePlaylistTorrentRequest;
use App\Models\Playlist;
use App\Models\PlaylistTorrent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PlaylistTorrentControllerTest
 */
class PlaylistTorrentController extends Controller
{
    /**
     * Attach A Torrent To A Playlist.
     */
    public function store(StorePlaylistTorrentRequest $request): \Illuminate\Http\RedirectResponse
    {
        $playlist = Playlist::query()->findOrFail($request->integer('playlist_id'));

        abort_unless($request->user()->id === $playlist->user_id, 403);

        $playlistTorrent = PlaylistTorrent::query()->create($request->validated());

        $playlistTorrent->torrent()->searchable();

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.attached-success'));
    }

    /**
     * Attach Torrents To A Playlist.
     */
    public function massUpsert(MassUpsertPlaylistTorrentRequest $request): \Illuminate\Http\RedirectResponse
    {
        $playlist = Playlist::query()->findOrFail($request->integer('playlist_id'));

        abort_unless($request->user()->id === $playlist->user_id, 403);

        $playlistTorrents = $request->string('torrent_urls')
            ->explode("\n")
            ->map(fn ($url) => basename($url))
            ->unique()
            ->map(fn ($id) => ['playlist_id' => $playlist->id, 'torrent_id' => $id, 'tmdb_id' => 0])
            ->toArray();

        Validator::make($playlistTorrents, [
            '*.torrent_id' => Rule::exists('torrents', 'id'),
        ], [
            '*.torrent_id.exists' => 'The torrent ID/URL ":input" entered was not found on site.'
        ])->validate();

        PlaylistTorrent::query()->upsert($playlistTorrents, ['playlist_id', 'torrent_id', 'tmdb_id']);

        $playlist->torrents()->searchable();

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.attached-success'));
    }

    /**
     * Detach A Torrent From A Playlist.
     *
     * @throws Exception
     */
    public function destroy(Request $request, PlaylistTorrent $playlistTorrent): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $playlistTorrent->playlist->user_id, 403);

        $playlistTorrent->delete();

        $playlistTorrent->torrent()->searchable();

        return to_route('playlists.show', ['playlist' => $playlistTorrent->playlist])
            ->with('success', trans('playlist.detached-success'));
    }
}
