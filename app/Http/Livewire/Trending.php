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

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\History;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class Trending extends Component
{
    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    #[Validate('in:movie_meta,tv_meta')]
    public string $metaType = 'movie_meta';

    #[Url(history: true)]
    #[Validate('in:day,week,weekly,month,monthly,year,release_year,all,custom')]
    public string $interval = 'day';

    #[Url(history: true)]
    #[Validate('sometimes|date_format:Y-m-d')]
    public string $from = '';

    #[Url(history: true)]
    #[Validate('sometimes|date_format:Y-m-d')]
    public string $until = '';

    public function updatingFrom(string &$value): void
    {
        try {
            $value = Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            $value = now()->subDay()->format('Y-m-d');
        }
    }

    public function updatingUntil(string &$value): void
    {
        try {
            $value = Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            $value = now()->format('Y-m-d');
        }
    }

    /**
     * @var Collection<int, Torrent>
     */
    final protected Collection $works {
        get {
            $this->validate();

            $metaIdColumn = match ($this->metaType) {
                'tv_meta' => 'tmdb_tv_id',
                default   => 'tmdb_movie_id',
            };

            return cache()->flexible(
                'trending-'.$this->interval.'-'.($this->from ?? '').'-'.($this->until ?? '').'-'.$this->metaType,
                [1800, 7200],
                fn () => Torrent::query()
                    ->with('movie', 'tv')
                    ->addSelect([
                        $metaIdColumn,
                        DB::raw('MIN(category_id) as category_id'),
                        DB::raw('COUNT(*) as download_count'),
                    ])
                    ->join('history', 'history.torrent_id', '=', 'torrents.id')
                    ->where($metaIdColumn, '!=', 0)
                    ->when($this->interval === 'day', fn ($query) => $query->whereBetween('history.completed_at', [now()->subDay(), now()]))
                    ->when($this->interval === 'week', fn ($query) => $query->whereBetween('history.completed_at', [now()->subWeek(), now()]))
                    ->when($this->interval === 'month', fn ($query) => $query->whereBetween('history.completed_at', [now()->subMonth(), now()]))
                    ->when($this->interval === 'year', fn ($query) => $query->whereBetween('history.completed_at', [now()->subYear(), now()]))
                    ->when($this->interval === 'all', fn ($query) => $query->whereNotNull('history.completed_at'))
                    ->when($this->interval === 'custom', fn ($query) => $query->whereBetween('history.completed_at', [$this->from ?: now(), $this->until ?: now()]))
                    ->whereRelation('category', $this->metaType, '=', true)
                    // Small torrents screw the stats since users download them only to farm bon.
                    ->where('torrents.size', '>', 1024 * 1024 * 1024)
                    ->groupBy($metaIdColumn)
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(250)
                    ->get($metaIdColumn)
            );
        }
    }

    /**
     * @var Collection<int|string, Collection<int, Torrent>>
     * @phpstan-ignore generics.notSubtype (I can't figure out the correct return type to silence this error)
     */
    final protected Collection $weekly {
        get {
            $this->validate();

            $metaIdColumn = match ($this->metaType) {
                'tv_meta' => 'tmdb_tv_id',
                default   => 'tmdb_movie_id',
            };

            return cache()->flexible(
                'weekly-charts:'.$this->metaType,
                [24 * 3600, 4 * 24 * 3600],
                fn () => Torrent::query()
                    ->withoutGlobalScopes()
                    ->with('movie', 'tv')
                    ->fromSub(
                        History::query()
                            ->withoutGlobalScopes()
                            ->join('torrents', 'torrents.id', '=', 'history.torrent_id')
                            ->join('categories', fn (JoinClause $join) => $join->on('torrents.category_id', '=', 'categories.id')->where($this->metaType, '=', true))
                            ->select([
                                DB::raw('FROM_DAYS(TO_DAYS(history.created_at) - MOD(TO_DAYS(history.created_at) - 1, 7)) AS week_start'),
                                $metaIdColumn,
                                DB::raw('MIN(categories.id) as category_id'),
                                DB::raw('COUNT(*) AS download_count'),
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY FROM_DAYS(TO_DAYS(history.created_at) - MOD(TO_DAYS(history.created_at) - 1, 7)) ORDER BY COUNT(*) DESC) AS place'),
                            ])
                            ->where($metaIdColumn, '!=', 0)
                            // Small torrents screw the stats since users download them only to farm bon.
                            ->where('torrents.size', '>', 1024 * 1024 * 1024)
                            ->groupBy('week_start', $metaIdColumn),
                        'ranked_groups',
                    )
                    ->where('place', '<=', 10)
                    ->orderByDesc('week_start')
                    ->orderBy('place')
                    ->withCasts([
                        'week_start' => 'datetime',
                    ])
                    ->get()
                    ->groupBy('week_start')
            );
        }
    }

    /**
     * @var Collection<int|string, Collection<int, Torrent>>
     * @phpstan-ignore generics.notSubtype (I can't figure out the correct return type to silence this error)
     */
    final protected Collection $monthly {
        get {
            $this->validate();

            $metaIdColumn = match ($this->metaType) {
                'tv_meta' => 'tmdb_tv_id',
                default   => 'tmdb_movie_id',
            };

            return cache()->flexible(
                'monthly-charts:'.$this->metaType,
                [24 * 3600, 4 * 24 * 3600],
                fn () => Torrent::query()
                    ->withoutGlobalScopes()
                    ->with($this->metaType === 'movie_meta' ? 'movie' : 'tv')
                    ->fromSub(
                        History::query()
                            ->withoutGlobalScopes()
                            ->join('torrents', 'torrents.id', '=', 'history.torrent_id')
                            ->join('categories', fn (JoinClause $join) => $join->on('torrents.category_id', '=', 'categories.id')->where($this->metaType, '=', true))
                            ->select([
                                DB::raw('EXTRACT(YEAR_MONTH FROM history.created_at) AS the_year_month'),
                                $metaIdColumn,
                                DB::raw('MIN(categories.id) as category_id'),
                                DB::raw('COUNT(*) AS download_count'),
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY EXTRACT(YEAR_MONTH FROM history.created_at) ORDER BY COUNT(*) DESC) AS place'),
                            ])
                            ->where($metaIdColumn, '!=', 0)
                            // Small torrents screw the stats since users download them only to farm bon.
                            ->where('torrents.size', '>', 1024 * 1024 * 1024)
                            ->groupBy('the_year_month', $metaIdColumn),
                        'ranked_groups',
                    )
                    ->where('place', '<=', 10)
                    ->orderByDesc('the_year_month')
                    ->orderBy('place')
                    ->get()
                    ->groupBy('the_year_month')
            );
        }
    }

    /**
     * @var Collection<int|string, Collection<int, Torrent>>
     * @phpstan-ignore generics.notSubtype (I can't figure out the correct return type to silence this error)
     */
    final protected Collection $releaseYear {
        get {
            $this->validate();

            $metaIdColumn = match ($this->metaType) {
                'tv_meta' => 'tmdb_tv_id',
                default   => 'tmdb_movie_id',
            };

            return cache()->flexible(
                'trending-by-release-year:'.$this->metaType,
                [24 * 3600, 4 * 24 * 3600],
                fn () => Torrent::query()
                    ->withoutGlobalScopes()
                    ->with($this->metaType === 'movie_meta' ? 'movie' : 'tv')
                    ->fromSub(
                        Torrent::query()
                            ->withoutGlobalScopes()
                            ->whereRelation('category', $this->metaType, '=', true)
                            ->leftJoin('tmdb_movies', 'torrents.tmdb_movie_id', '=', 'tmdb_movies.id')
                            ->leftJoin('tmdb_tv', 'torrents.tmdb_tv_id', '=', 'tmdb_tv.id')
                            ->select([
                                $metaIdColumn,
                                DB::raw('MIN(category_id) as category_id'),
                                DB::raw('SUM(times_completed) AS download_count'),
                                'the_year' => $this->metaType === 'movie_meta'
                                    ? TmdbMovie::query()
                                        ->selectRaw('EXTRACT(YEAR FROM tmdb_movies.release_date)')
                                        ->whereColumn('tmdb_movies.id', '=', 'torrents.tmdb_movie_id')
                                    : TmdbTv::query()
                                        ->selectRaw('EXTRACT(YEAR FROM tmdb_tv.first_air_date)')
                                        ->whereColumn('tmdb_tv.id', '=', 'torrents.tmdb_tv_id'),
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY COALESCE(EXTRACT(YEAR FROM MAX(tmdb_movies.release_date)), EXTRACT(YEAR FROM MAX(tmdb_tv.first_air_date))) ORDER BY SUM(times_completed) DESC) AS place'),
                            ])
                            ->where($metaIdColumn, '!=', 0)
                            // Small torrents screw the stats since users download them only to farm bon.
                            ->where('torrents.size', '>', 2 * 1024 * 1024 * 1024)
                            ->when($this->metaType === 'tv_meta', fn ($query) => $query->where('episode_number', '=', 0))
                            ->havingNotNull('the_year')
                            ->where(fn ($query) => $query->whereNotNull('tmdb_movies.id')->orWhereNotNull('tmdb_tv.id'))
                            ->groupBy('the_year', $metaIdColumn),
                        'ranked_groups',
                    )
                    ->where('place', '<=', 10)
                    ->orderByDesc('the_year')
                    ->orderBy('place')
                    ->get()
                    ->groupBy('the_year')
            );
        }
    }

    /**
     * @var array<string, string>
     */
    final protected array $metaTypes {
        get {
            $metaTypes = [];

            if (Category::where('movie_meta', '=', true)->exists()) {
                $metaTypes[(string) __('mediahub.movie')] = 'movie_meta';
            }

            if (Category::where('tv_meta', '=', true)->exists()) {
                $metaTypes[(string) __('mediahub.show')] = 'tv_meta';
            }

            return $metaTypes;
        }
    }

    final public function placeholder(): string
    {
        return <<<'HTML'
        <section class="panelV2">
            <h2 class="panel__heading">Top Titles</h2>
            <div class="panel__body">Loading...</div>
        </section>
        HTML;
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.trending', [
            'user'  => auth()->user(),
            'works' => match ($this->interval) {
                'weekly'       => $this->weekly,
                'monthly'      => $this->monthly,
                'release_year' => $this->releaseYear,
                default        => $this->works,
            },
            'metaTypes' => $this->metaTypes,
        ]);
    }
}
