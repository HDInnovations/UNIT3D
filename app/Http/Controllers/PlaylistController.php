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

use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Models\TmdbMovie;
use App\Models\Playlist;
use App\Models\PlaylistCategory;
use App\Models\TmdbTv;
use App\Repositories\ChatRepository;
use App\Traits\TorrentMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PlaylistControllerTest
 */
class PlaylistController extends Controller
{
    use TorrentMeta;

    /**
     * PlaylistController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Display All Playlists.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('playlist.index');
    }

    /**
     * Show Playlist Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('playlist.create', [
            'playlistCategories' => PlaylistCategory::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Store A New Playlist.
     */
    public function store(StorePlaylistRequest $request): \Illuminate\Http\RedirectResponse
    {
        if ($request->hasFile('cover_image')) {
            abort_if(\is_array($request->file('cover_image')), 400);

            abort_unless($request->file('cover_image')->getError() === UPLOAD_ERR_OK, 500);

            $image = $request->file('cover_image');
            $filename = 'playlist-cover_'.uniqid('', true).'.'.$image->getClientOriginalExtension();
            Storage::disk('playlist-images')->put($filename, Image::fromUpload($image)->cover(400, 225)->toPng()->quality(100)->toBytes());
        }

        $playlist = Playlist::create([
            'user_id'     => $request->user()->id,
            'cover_image' => $filename ?? null
        ] + $request->validated());

        // Announce To Shoutbox
        if (!$playlist->is_private) {
            $this->chatRepository->systemMessage(
                trans('application-messages.bot.playlist-created', [
                    'userUrl'     => \sprintf('%s/users/%s.%s', config('app.url'), $request->user()->username, $request->user()->id),
                    'username'    => $request->user()->username,
                    'playlistUrl' => \sprintf('%s/playlists/%s', config('app.url'), $playlist->id),
                    'name'        => $playlist->name,
                ], config('app.locale'))
            );
        }

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.published-success'));
    }

    /**
     * Show A Playlist.
     */
    public function show(Request $request, Playlist $playlist): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_if($playlist->is_private && $playlist->user_id !== $request->user()->id && !$request->user()->group->is_modo, 403, trans('playlist.private-error'));

        $randomTorrent = $playlist->torrents()->inRandomOrder()->with('category')->first();

        $torrents = $playlist->torrents()
            ->select('*')
            ->selectRaw(<<<'SQL'
                CASE
                    WHEN category_id IN (SELECT id from categories where movie_meta = TRUE) THEN 'movie'
                    WHEN category_id IN (SELECT id from categories where tv_meta = TRUE) THEN 'tv'
                    WHEN category_id IN (SELECT id from categories where game_meta = TRUE) THEN 'game'
                    WHEN category_id IN (SELECT id from categories where music_meta = TRUE) THEN 'music'
                    WHEN category_id IN (SELECT id from categories where no_meta = TRUE) THEN 'no'
                END as meta
            SQL)
            ->when($request->has('search'), fn ($query) => $query->where('name', 'LIKE', '%'.str_replace(' ', '%', $request->search).'%'))
            ->with(['category', 'resolution', 'type', 'user.group'])
            ->orderBy('name')
            ->paginate(26);

        // See app/Traits/TorrentMeta.php
        $this->scopeMeta($torrents);

        return view('playlist.show', [
            'playlist' => $playlist->load(['user.group', 'suggestions' => ['user.group', 'torrent']]),
            'meta'     => match (true) {
                $randomTorrent?->category?->tv_meta    => TmdbTv::find($randomTorrent->tmdb_tv_id),
                $randomTorrent?->category?->movie_meta => TmdbMovie::find($randomTorrent->tmdb_movie_id),
                default                                => null,
            },
            'latestPlaylistTorrent' => $playlist->torrents()->orderByPivot('created_at', 'desc')->first(),
            'torrents'              => $torrents,
            'search'                => $request->search,
        ]);
    }

    /**
     * Show Playlist Update Form.
     */
    public function edit(Request $request, Playlist $playlist): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->id === $playlist->user_id || $request->user()->group->is_modo, 403);

        return view('playlist.edit', [
            'playlist'           => $playlist,
            'playlistCategories' => PlaylistCategory::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Update A Playlist.
     */
    public function update(UpdatePlaylistRequest $request, Playlist $playlist): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->id == $playlist->user_id || $request->user()->group->is_modo, 403);

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');

            abort_if(\is_array($image), 400);

            abort_unless($image->getError() === UPLOAD_ERR_OK, 500);

            $filename = 'playlist-cover_'.uniqid('', true).'.'.$image->getClientOriginalExtension();
            Storage::disk('playlist-images')->put($filename, Image::fromUpload($image)->cover(400, 225)->toPng()->quality(100)->toBytes());

            if ($playlist->cover_image !== null) {
                Storage::disk('playlist-images')->delete($playlist->cover_image);
            }

            $playlist->update(['cover_image' => $filename] + $request->validated());
        } else {
            $playlist->update($request->validated());
        }

        return to_route('playlists.show', ['playlist' => $playlist])
            ->with('success', trans('playlist.update-success'));
    }

    /**
     * Delete A Playlist.
     *
     * @throws Exception
     */
    public function destroy(Request $request, Playlist $playlist): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->id == $playlist->user_id || $request->user()->group->is_modo, 403);

        $playlist->torrents()->detach();
        $playlist->comments()->delete();
        $playlist->delete();

        return to_route('playlists.index')
            ->with('success', trans('playlist.deleted'));
    }
}
