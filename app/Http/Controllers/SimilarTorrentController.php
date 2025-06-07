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

use App\Models\Category;
use App\Models\IgdbGame;
use App\Models\TmdbMovie;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\TmdbTv;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use Illuminate\Http\Request;

class SimilarTorrentController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(int $categoryId, int $tmdbId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $category = Category::query()->findOrFail($categoryId);

        switch (true) {
            case $category->movie_meta:
                $hasTorrents = Torrent::query()->where('category_id', '=', $categoryId)->where('tmdb_movie_id', '=', $tmdbId)->exists();

                abort_unless($hasTorrents, 404, 'No Similar Torrents Found');

                $meta = TmdbMovie::with([
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'collections.movies',
                ])
                    ->findOrFail($tmdbId);
                $tmdb = $tmdbId;

                break;
            case $category->tv_meta:
                $hasTorrents = Torrent::query()->where('category_id', '=', $categoryId)->where('tmdb_tv_id', '=', $tmdbId)->exists();

                abort_unless($hasTorrents, 404, 'No Similar Torrents Found');

                $meta = TmdbTv::with([
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'networks'
                ])
                    ->findOrFail($tmdbId);
                $tmdb = $tmdbId;

                break;
            case $category->game_meta:
                $hasTorrents = Torrent::query()->where('category_id', '=', $categoryId)->where('igdb', '=', $tmdbId)->exists();

                abort_unless($hasTorrents, 404, 'No Similar Torrents Found');

                $meta = IgdbGame::with([
                    'genres',
                    'companies',
                    'platforms',
                ])
                    ->findOrFail($tmdbId);

                $igdb = $tmdbId;

                break;
            default:
                abort(404, 'No Similar Torrents Found');
        }

        $personalFreeleech = cache()->get('personal_freeleech:'.auth()->id());

        return view('torrent.similar', [
            'meta'               => $meta,
            'personal_freeleech' => $personalFreeleech,
            'category'           => $category,
            'tmdb'               => $tmdb ?? null,
            'igdb'               => $igdb ?? null,
        ]);
    }

    public function update(Request $request, Category $category, int $metaId): \Illuminate\Http\RedirectResponse
    {
        if (!($category->movie_meta || $category->tv_meta || $category->game_meta)) {
            return to_route('torrents.similar', ['category_id' => $category->id, 'tmdb' => $metaId])
                ->withErrors('This meta type can not be updated.');
        }

        if (
            $metaId === 0
            || (
                $category->movie_meta
                && Torrent::where('category_id', '=', $category->id)->where('tmdb_movie_id', '=', $metaId)->doesntExist()
                && TorrentRequest::where('category_id', '=', $category->id)->where('tmdb_movie_id', '=', $metaId)->doesntExist()
            )
            || (
                $category->tv_meta
                && Torrent::where('category_id', '=', $category->id)->where('tmdb_tv_id', '=', $metaId)->doesntExist()
                && TorrentRequest::where('category_id', '=', $category->id)->where('tmdb_tv_id', '=', $metaId)->doesntExist()
            )
            || (
                $category->game_meta
                && Torrent::where('category_id', '=', $category->id)->where('igdb', '=', $metaId)->doesntExist()
                && TorrentRequest::where('category_id', '=', $category->id)->where('igdb', '=', $metaId)->doesntExist()
            )
        ) {
            return to_route('torrents.similar', ['category_id' => $category->id, 'tmdb' => $metaId])
                ->withErrors('There exists no torrent with this tmdb.');
        }

        /** @phpstan-ignore match.unhandled (The first line of this method ensures that at least one of these are true) */
        match (true) {
            $category->movie_meta => new TMDBScraper()->movie($metaId),
            $category->tv_meta    => new TMDBScraper()->tv($metaId),
            $category->game_meta  => new IgdbScraper()->game($metaId),
        };

        return back()->with('success', 'Metadata update queued successfully.');
    }
}
