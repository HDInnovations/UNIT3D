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

namespace App\Traits;

use App\Models\IgdbGame;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use JsonException;
use ReflectionException;

trait TorrentMeta
{
    /**
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Torrent>|\Illuminate\Pagination\CursorPaginator<int, \App\Models\Torrent>|\Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\Torrent>|\Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Torrent> $torrents
     *
     * @throws \MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException
     * @throws \MarcReichel\IGDBLaravel\Exceptions\InvalidParamsException
     * @throws ReflectionException
     * @throws JsonException
     * @return (
     *        $torrents is \Illuminate\Database\Eloquent\Collection<int, \App\Models\Torrent> ? \Illuminate\Support\Collection<int, \App\Models\Torrent>
     *     : ($torrents is \Illuminate\Pagination\CursorPaginator<int, \App\Models\Torrent> ? \Illuminate\Pagination\CursorPaginator<int, \App\Models\Torrent>
     *     : ($torrents is \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\Torrent> ? \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\Torrent>
     *     : \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Torrent>
     * )))
     */
    public function scopeMeta(\Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\CursorPaginator|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator $torrents, bool $withCredits = false): \Illuminate\Support\Collection|\Illuminate\Pagination\CursorPaginator|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if ($torrents instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator || $torrents instanceof \Illuminate\Contracts\Pagination\CursorPaginator) {
            $movieIds = collect($torrents->items())->where('meta', '=', 'movie')->pluck('tmdb_movie_id');
            $tvIds = collect($torrents->items())->where('meta', '=', 'tv')->pluck('tmdb_tv_id');
            $gameIds = collect($torrents->items())->where('meta', '=', 'game')->pluck('igdb');
        } else {
            $movieIds = $torrents->where('meta', '=', 'movie')->pluck('tmdb_movie_id');
            $tvIds = $torrents->where('meta', '=', 'tv')->pluck('tmdb_tv_id');
            $gameIds = $torrents->where('meta', '=', 'game')->pluck('igdb');
        }

        $movies = TmdbMovie::query()
            ->with('genres')
            ->when($withCredits, fn ($query) => $query->with([
                'actors'    => fn ($query) => $query->limit(3),
                'directors' => fn ($query) => $query->limit(3),
            ]))
            ->whereIntegerInRaw('id', $movieIds)
            ->get()
            ->keyBy('id');
        $tv = TmdbTv::query()
            ->with('genres')
            ->when($withCredits, fn ($query) => $query->with([
                'actors'   => fn ($query) => $query->limit(3),
                'creators' => fn ($query) => $query->limit(3),
            ]))
            ->whereIntegerInRaw('id', $tvIds)
            ->get()
            ->keyBy('id');
        $games = IgdbGame::query()
            ->with('genres')
            ->whereIntegerInRaw('id', $gameIds)
            ->get()
            ->keyBy('id');

        $setRelation = function ($torrent) use ($movies, $tv, $games) {
            $torrent->setAttribute(
                'meta',
                match ($torrent->meta) {
                    'movie' => $movies[$torrent->tmdb_movie_id] ?? null,
                    'tv'    => $tv[$torrent->tmdb_tv_id] ?? null,
                    'game'  => $games[$torrent->igdb] ?? null,
                    default => null,
                },
            );

            return $torrent;
        };

        if ($torrents instanceof \Illuminate\Database\Eloquent\Collection) {
            return $torrents->map($setRelation);
        }

        /**
         * Laravel's \Illuminate\Contracts\Pagination\LengthAwarePaginator does not have a through method
         * but we are passed a \Illuminate\Pagination\LengthAwarePaginator which does have such a method.
         * Seems to be caused by some Laravel type error that's returning an interface instead of the type
         * itself, or that the interface is missing the method.
         *
         * @phpstan-ignore method.notFound
         */
        return $torrents->through($setRelation);
    }
}
