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
use App\Models\TmdbGenre;
use App\Models\TmdbMovie;
use App\Models\Resolution;
use App\Models\TorrentRequest;
use App\Models\Type;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TorrentRequestSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $name = '';

    #[Url(history: true)]
    public string $requestor = '';

    /**
     * @var array<int>
     */
    #[Url(history: true)]
    public array $categoryIds = [];

    /**
     * @var array<int>
     */
    #[Url(history: true)]
    public array $typeIds = [];

    /**
     * @var array<int>
     */
    #[Url(history: true)]
    public array $resolutionIds = [];

    /**
     * @var array<int>
     */
    #[Url(history: true)]
    public array $genreIds = [];

    /**
     * @var array<string>
     */
    #[Url(history: true)]
    public array $primaryLanguageNames = [];

    #[Url(history: true)]
    public ?int $tmdbId = null;

    #[Url(history: true)]
    public string $imdbId = '';

    #[Url(history: true)]
    public ?int $tvdbId = null;

    #[Url(history: true)]
    public ?int $malId = null;

    #[Url(history: true)]
    public bool $unfilled = false;

    #[Url(history: true)]
    public bool $claimed = false;

    #[Url(history: true)]
    public bool $pending = false;

    #[Url(history: true)]
    public bool $filled = false;

    #[Url(history: true)]
    public bool $myRequests = false;

    #[Url(history: true)]
    public bool $myClaims = false;

    #[Url(history: true)]
    public bool $myVoted = false;

    #[Url(history: true)]
    public bool $myFilled = false;

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);

        $this->resetPage();
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Category>
     */
    final protected \Illuminate\Database\Eloquent\Collection $categories {
        get => cache()->flexible(
            'categories',
            [3600, 3600 * 2],
            fn () => Category::query()->orderBy('position')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Type>
     */
    final protected \Illuminate\Database\Eloquent\Collection $types {
        get => cache()->flexible(
            'types',
            [3600, 3600 * 2],
            fn () => Type::query()->orderBy('position')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Resolution>
     */
    final protected \Illuminate\Database\Eloquent\Collection $resolutions {
        get => cache()->flexible(
            'resolutions',
            [3600, 3600 * 2],
            fn () => Resolution::query()->orderBy('position')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, TmdbGenre>
     */
    final protected \Illuminate\Database\Eloquent\Collection $genres {
        get => cache()->flexible(
            'genres',
            [3600, 3600 * 2],
            fn () => TmdbGenre::query()->orderBy('name')->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, string>
     */
    final protected \Illuminate\Support\Collection $primaryLanguages {
        get => cache()->flexible(
            'original-languages',
            [3600, 3600 * 2],
            fn () => TmdbMovie::query()
                ->select('original_language')
                ->distinct()
                ->orderBy('original_language')
                ->pluck('original_language'),
        );
    }

    final protected ?object $torrentRequestStat {
        get => DB::table('requests')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when filled_by is not null then 1 end) as filled')
            ->selectRaw('count(case when filled_by is null then 1 end) as unfilled')
            ->first();
    }

    final protected ?object $torrentRequestBountyStat {
        get => DB::table('requests')
            ->selectRaw('coalesce(sum(bounty), 0) as total')
            ->selectRaw('coalesce(sum(case when filled_by is not null then bounty end), 0) as claimed')
            ->selectRaw('coalesce(sum(case when filled_by is null then bounty end), 0) as unclaimed')
            ->first();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, TorrentRequest>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $torrentRequests {
        get {
            $user = auth()->user();
            $isRegexAllowed = $user->group->is_modo;
            $isRegex = fn ($field) => $isRegexAllowed
                && \strlen((string) $field) > 2
                && $field[0] === '/'
                && $field[-1] === '/'
                && @preg_match($field, 'Validate regex') !== false;

            return TorrentRequest::with(['user:id,username,group_id', 'user.group', 'category', 'type', 'resolution'])
                ->withCount(['comments', 'bounties'])
                ->withExists('claim')
                ->when(
                    $this->name !== '',
                    fn ($query) => $query
                        ->when(
                            $isRegex($this->name),
                            fn ($query) => $query->where('name', 'REGEXP', substr($this->name, 1, -1)),
                            fn ($query) => $query->where('name', 'LIKE', '%'.str_replace(' ', '%', $this->name).'%')
                        )
                )
                ->when(
                    $this->requestor !== '',
                    fn ($query) => $query
                        ->whereRelation('user', 'username', '=', $this->requestor)
                        ->when(
                            $user === null,
                            fn ($query) => $query->where('anon', '=', false),
                            fn ($query) => $query
                                ->when(
                                    !$user->group->is_modo,
                                    fn ($query) => $query
                                        ->where(
                                            fn ($query) => $query
                                                ->where('anon', '=', false)
                                                ->orWhere('user_id', '=', $user->id)
                                        )
                                )
                        )
                )
                ->when($this->categoryIds !== [], fn ($query) => $query->whereIntegerInRaw('category_id', $this->categoryIds))
                ->when($this->typeIds !== [], fn ($query) => $query->whereIntegerInRaw('type_id', $this->typeIds))
                ->when($this->resolutionIds !== [], fn ($query) => $query->whereIntegerInRaw('resolution_id', $this->resolutionIds))
                ->when($this->tmdbId !== null, fn ($query) => $query->where(fn ($query) => $query->where('tmdb_movie_id', '=', $this->tmdbId)->orWhere('tmdb_tv_id', '=', $this->tmdbId)))
                ->when($this->imdbId !== '', fn ($query) => $query->where('imdb', '=', (preg_match('/tt0*(\d{7,})/', $this->imdbId, $matches) ? $matches[1] : $this->imdbId)))
                ->when($this->tvdbId !== null, fn ($query) => $query->where('tvdb', '=', $this->tvdbId))
                ->when($this->malId !== null, fn ($query) => $query->where('mal', '=', $this->malId))
                ->when(
                    $this->genreIds !== [],
                    fn ($query) => $query
                        ->where(
                            fn ($query) => $query
                                ->where(
                                    fn ($query) => $query
                                        ->whereRelation('category', 'movie_meta', '=', true)
                                        ->whereIn('tmdb_movie_id', DB::table('tmdb_genre_tmdb_movie')->select('tmdb_movie_id')->whereIn('tmdb_genre_id', $this->genreIds))
                                )
                                ->orWhere(
                                    fn ($query) => $query
                                        ->whereRelation('category', 'tv_meta', '=', true)
                                        ->whereIn('tmdb_tv_id', DB::table('tmdb_genre_tmdb_tv')->select('tmdb_tv_id')->whereIn('tmdb_genre_id', $this->genreIds))
                                )
                        )
                )
                ->when(
                    $this->primaryLanguageNames !== [],
                    fn ($query) => $query
                        ->where(
                            fn ($query) => $query
                                ->where(
                                    fn ($query) => $query
                                        ->whereRelation('category', 'movie_meta', '=', true)
                                        ->whereHas('movie', fn ($query) => $query->whereIn('original_language', $this->primaryLanguageNames))
                                )
                                ->orWhere(
                                    fn ($query) => $query
                                        ->whereRelation('category', 'tv_meta', '=', true)
                                        ->whereHas('tv', fn ($query) => $query->whereIn('original_language', $this->primaryLanguageNames))
                                )
                        )
                )
                ->when($this->unfilled || $this->claimed || $this->pending || $this->filled, function ($query): void {
                    $query->where(function ($query): void {
                        $query->where(function ($query): void {
                            if ($this->unfilled) {
                                $query->whereNull('torrent_id')->whereDoesntHave('claim');
                            }
                        })
                            ->orWhere(function ($query): void {
                                if ($this->claimed) {
                                    $query->whereHas('claim')->whereNull('torrent_id')->whereNull('approved_when');
                                }
                            })
                            ->orWhere(function ($query): void {
                                if ($this->pending) {
                                    $query->whereNotNull('torrent_id')->whereNull('approved_when');
                                }
                            })
                            ->orWhere(function ($query): void {
                                if ($this->filled) {
                                    $query->whereNotNull('torrent_id')->whereNotNull('approved_when');
                                }
                            });
                    });
                })
                ->when($this->myRequests, function ($query) use ($user): void {
                    $query->where('user_id', '=', $user->id);
                })
                ->when($this->myClaims, function ($query) use ($user): void {
                    $query->whereRelation('claim', 'user_id', '=', $user->id)->whereNull('torrent_id')->whereNull('approved_by');
                })
                ->when($this->myVoted, function ($query) use ($user): void {
                    $query->whereRelation('bounties', 'user_id', '=', $user->id);
                })
                ->when($this->myFilled, function ($query) use ($user): void {
                    $query->where('filled_by', '=', $user->id);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-request-search', [
            'user'                     => auth()->user(),
            'categories'               => $this->categories,
            'types'                    => $this->types,
            'resolutions'              => $this->resolutions,
            'genres'                   => $this->genres,
            'primaryLanguages'         => $this->primaryLanguages,
            'torrentRequests'          => $this->torrentRequests,
            'torrentRequestStat'       => $this->torrentRequestStat,
            'torrentRequestBountyStat' => $this->torrentRequestBountyStat,
        ]);
    }
}
