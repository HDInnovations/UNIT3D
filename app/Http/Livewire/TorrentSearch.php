<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.tx
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\DTO\TorrentSearchFiltersDTO;
use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\MediaWork;
use App\Models\TmdbGenre;
use App\Models\TmdbMovie;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use App\Traits\TorrentMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Illuminate\Support\Facades\DB;
use Closure;

class TorrentSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use TorrentMeta;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $name = '';

    #[Url(history: true)]
    public string $description = '';

    #[Url(history: true)]
    public string $mediainfo = '';

    #[Url(history: true)]
    public string $uploader = '';

    #[Url(history: true)]
    public string $keywords = '';

    #[Url(history: true)]
    public ?int $startYear = null;

    #[Url(history: true)]
    public ?int $endYear = null;

    #[Url(history: true)]
    public ?int $minSize = null;

    #[Url(history: true)]
    public int $minSizeMultiplier = 1;

    #[Url(history: true)]
    public ?int $maxSize = null;

    #[Url(history: true)]
    public int $maxSizeMultiplier = 1;

    #[Url(history: true)]
    public ?int $episodeNumber = null;

    #[Url(history: true)]
    public ?int $seasonNumber = null;

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
     * @var array<int>
     */
    #[Url(history: true)]
    public array $regionIds = [];

    /**
     * @var array<int>
     */
    #[Url(history: true)]
    public array $distributorIds = [];

    // Category-specific normalized-fact filters (see MediaWorkCatalog::computeFacets);
    // only rendered/applied for their own category kind, cleared on any change to
    // categoryIds that makes them inapplicable (see updatedCategoryIds()).
    #[Url(history: true)]
    public string $musicArtist = '';

    #[Url(history: true)]
    public string $musicLabel = '';

    #[Url(history: true)]
    public string $musicFormat = '';

    #[Url(history: true)]
    public ?int $musicYear = null;

    #[Url(history: true)]
    public string $gamePlatform = '';

    #[Url(history: true)]
    public string $gameGenre = '';

    #[Url(history: true)]
    public string $gameDeveloper = '';

    #[Url(history: true)]
    public string $bookAuthor = '';

    #[Url(history: true)]
    public string $bookLanguage = '';

    #[Url(history: true)]
    public string $bookPublisher = '';

    #[Url(history: true)]
    public string $adult = 'any';

    #[Url(history: true)]
    public ?int $tmdbId = null;

    #[Url(history: true)]
    public string $imdbId = '';

    #[Url(history: true)]
    public ?int $tvdbId = null;

    #[Url(history: true)]
    public ?int $malId = null;

    #[Url(history: true)]
    public ?int $playlistId = null;

    #[Url(history: true)]
    public ?int $collectionId = null;

    #[Url(history: true)]
    public ?int $networkId = null;

    #[Url(history: true)]
    public ?int $companyId = null;

    /**
     * @var string[]
     */
    #[Url(history: true)]
    public array $primaryLanguageNames = [];

    /**
     * @var string[]
     */
    #[Url(history: true)]
    public array $free = [];

    #[Url(history: true)]
    public bool $doubleup = false;

    #[Url(history: true)]
    public bool $featured = false;

    #[Url(history: true)]
    public bool $refundable = false;

    #[Url(history: true)]
    public bool $highspeed = false;

    #[Url(history: true)]
    public bool $bookmarked = false;

    #[Url(history: true)]
    public bool $wished = false;

    #[Url(history: true)]
    public bool $internal = false;

    #[Url(history: true)]
    public bool $personalRelease = false;

    #[Url(history: true)]
    public bool $trumpable = false;

    #[Url(history: true)]
    public bool $alive = false;

    #[Url(history: true)]
    public bool $dying = false;

    #[Url(history: true)]
    public bool $dead = false;

    #[Url(history: true)]
    public bool $graveyard = false;

    #[Url(history: true)]
    public bool $notDownloaded = false;

    #[Url(history: true)]
    public bool $downloaded = false;

    #[Url(history: true)]
    public bool $seeding = false;

    #[Url(history: true)]
    public bool $leeching = false;

    #[Url(history: true)]
    public bool $incomplete = false;

    #[Url(history: true, except: 'meilisearch')]
    public ?string $driver = 'meilisearch';

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(except: 'bumped_at')]
    public string $sortField = 'bumped_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(except: 'list')]
    public string $view = 'list';

    /**
     * Bounded torrent-variant ids currently on the page (see $workGroups);
     * populated every time a real search runs. Used only as the candidate
     * set for refreshDisplayedStats()'s live-stats poll, which re-validates
     * moderation/soft-delete status against the database on every single
     * poll rather than trusting this list to still be accurate — a torrent
     * can be unapproved or deleted between searches. #[Locked] so a
     * tampered client request can never substitute ids the server didn't
     * itself just search for, bounding the poll to what this user was
     * already shown.
     *
     * @var array<int>
     */
    #[Locked]
    public array $displayedTorrentIds = [];

    /**
     * Refreshes only the live per-torrent statistics (seeders/leechers/times
     * completed/current-user activity) and the global health counters for
     * the torrents already on the page, without re-running the filtered
     * Meilisearch/SQL search and without re-rendering any HTML at all
     * (#[Renderless]): the client applies the dispatched counts directly to
     * the existing DOM cells (see the torrent-stats-refreshed listener in
     * torrent-search.blade.php). Every candidate id is re-checked against
     * current moderation status and soft-deletes so a torrent unapproved or
     * deleted after the search is never disclosed by a later poll.
     */
    #[Renderless]
    final public function refreshDisplayedStats(): void
    {
        $torrents = $this->displayedTorrentIds === []
            ? collect()
            : $this->liveTorrentStats($this->displayedTorrentIds);

        $this->dispatch(
            'torrent-stats-refreshed',
            torrents: $torrents->all(),
            health: (array) $this->torrentHealth,
        );
    }

    /**
     * Live, privacy/authorization-correct stats for a bounded set of
     * candidate torrent ids: re-applies the moderation/soft-delete gate (the
     * ids themselves may be stale by the time this runs) and the
     * current-user-scoped seeding/leeching/completed flags, exactly as the
     * initial search does, but without any name/category/filter predicate.
     *
     * @param array<int> $torrentIds
     *
     * @return \Illuminate\Support\Collection<int, array{id: int, seeders: int, leechers: int, timesCompleted: int, seeding: bool, leeching: bool, userCompleted: bool}>
     */
    private function liveTorrentStats(array $torrentIds): \Illuminate\Support\Collection
    {
        $user = auth()->user();

        return Torrent::query()
            ->whereIntegerInRaw('id', $torrentIds)
            ->where('status', '=', \App\Enums\ModerationStatus::APPROVED)
            ->whereNull('deleted_at')
            ->withExists([
                'history as seeding' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 1),
                'history as leeching' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 0),
                'history as completed' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 0)
                    ->where('seeder', '=', 1),
            ])
            ->get(['id', 'seeders', 'leechers', 'times_completed'])
            ->map(fn (Torrent $torrent) => [
                'id'             => $torrent->id,
                'seeders'        => $torrent->seeders,
                'leechers'       => $torrent->leechers,
                'timesCompleted' => $torrent->times_completed,
                'seeding'        => (bool) $torrent->getAttributeValue('seeding'),
                'leeching'       => (bool) $torrent->getAttributeValue('leeching'),
                'userCompleted'  => (bool) $torrent->getAttributeValue('completed'),
            ]);
    }

    /**
     * Get torrent health statistics.
     */
    #[Computed]
    final protected function torrentHealth(): object
    {
        return cache()->flexible(
            'torrent-search:health',
            // Short-lived: the results panel polls, and alive/dead follows announces within seconds.
            [15, 30],
            fn () => DB::table('torrents')
                ->whereNull('deleted_at')
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw('SUM(seeders > 0) AS alive')
                ->selectRaw('SUM(seeders = 0) AS dead')
                ->first(),
        );
    }

    final public function mount(Request $request): void
    {
        if ($request->missing('sortField')) {
            $this->sortField = auth()->user()->settings->torrent_sort_field;
        }

        if ($request->missing('view')) {
            $this->view = match (auth()->user()->settings->torrent_layout) {
                1       => 'card',
                2       => 'group',
                3       => 'poster',
                default => 'list',
            };
        }

        // Livewire's updated*() hooks never fire for the initial hydration from
        // the URL, so a deep link that explicitly pins categoryIds to a
        // non-video/non-music/non-game/non-book kind alongside a now-inapplicable
        // filter (e.g. ?categoryIds[]=<music>&genreIds[]=28) must be sanitized here
        // too, not just on later user-driven category changes.
        $this->sanitizeCategorySpecificFilters();
    }

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);

        $this->resetPage();
    }

    final public function updatedView(): void
    {
        $this->perPage = \in_array($this->view, ['card', 'poster']) ? 24 : 25;
    }

    /**
     * Category-specific normalized-fact filters only make sense for their
     * own category kind (music/game/book): clear whichever ones no longer
     * apply whenever the category selection changes, so switching away
     * from Music doesn't leave a stale, invisible artist filter silently
     * excluding every result.
     */
    final public function updatedCategoryIds(): void
    {
        $this->sanitizeCategorySpecificFilters();
    }

    /**
     * Clear every category-specific filter that no longer applies to the
     * currently selected categoryIds, whether they arrived via a user-driven
     * category change or an explicit-category deep link. With no category
     * selected, every kind is applicable, so nothing is cleared.
     *
     * Music/game/book facet filters (see MediaWorkCatalog::computeFacets) are
     * only ever sent by the UI for their own kind. Movie/TV filters (genreIds,
     * collectionId, companyId, networkId, primaryLanguageNames, adult,
     * startYear/endYear) gate toSqlQueryBuilder()'s whole query on
     * category.movie_meta/tv_meta (see TorrentSearchFiltersDTO), so left set
     * after switching away from Movie/TV they would silently zero out every
     * Music/Game/Book/XXX result. tvdbId/seasonNumber/episodeNumber are TV-only.
     * resolutionIds is a "technical" filter: resolution_id is only ever
     * populated for movie/tv/xxx uploads (see StoreTorrentRequest), so a
     * stale selection would equally starve every other kind.
     *
     * Provider IDs and video region/distributor constraints are cleared only
     * for explicit non-video categories. Unscoped provider-ID deep links remain
     * valid because the empty-category case returns before any reset.
     */
    private function sanitizeCategorySpecificFilters(): void
    {
        if ($this->categoryIds === []) {
            return;
        }

        $kinds = Category::query()
            ->whereIntegerInRaw('id', $this->categoryIds)
            ->get()
            ->map(fn (Category $category): string => UploadKinds::categoryKind($category));

        if ($this->typeIds !== []) {
            $this->typeIds = Type::query()->whereIntegerInRaw('id', $this->typeIds)->get()
                ->filter(fn (Type $type): bool => $kinds->intersect(UploadKinds::typeKinds($type->name))->isNotEmpty())
                ->pluck('id')->all();
        }

        if (!$kinds->contains('music')) {
            $this->reset(['musicArtist', 'musicLabel', 'musicFormat', 'musicYear']);
        }

        if (!$kinds->contains('game')) {
            $this->reset(['gamePlatform', 'gameGenre', 'gameDeveloper']);
        }

        if (!$kinds->contains('book')) {
            $this->reset(['bookAuthor', 'bookLanguage', 'bookPublisher']);
        }

        if (!$kinds->contains('movie')) {
            $this->reset(['collectionId']);
        }

        if (!$kinds->contains('tv')) {
            $this->reset(['networkId', 'tvdbId', 'seasonNumber', 'episodeNumber']);
        }

        if (!$kinds->contains('movie') && !$kinds->contains('tv')) {
            $this->reset(['genreIds', 'companyId', 'primaryLanguageNames', 'startYear', 'endYear', 'adult', 'tmdbId', 'imdbId', 'malId', 'regionIds', 'distributorIds']);
        }

        if (!$kinds->contains('movie') && !$kinds->contains('tv') && !$kinds->contains('xxx')) {
            $this->reset(['resolutionIds']);
        }
    }

    #[Computed]
    final protected function personalFreeleech(): bool
    {
        return cache()->get('personal_freeleech:'.auth()->id()) ?? false;
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
     * @var \Illuminate\Database\Eloquent\Collection<int, Resolution>
     */
    final protected \Illuminate\Database\Eloquent\Collection $genres {
        get => cache()->flexible(
            'genres',
            [3600, 3600 * 2],
            fn () => TmdbGenre::query()->orderBy('name')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Region>
     */
    final protected \Illuminate\Database\Eloquent\Collection $regions {
        get => cache()->flexible(
            'regions',
            [3600, 3600 * 2],
            fn () => Region::query()->orderBy('position')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Distributor>
     */
    final protected \Illuminate\Database\Eloquent\Collection $distributors {
        get => cache()->flexible(
            'distributors',
            [3600, 3600 * 2],
            fn () => Distributor::query()->orderBy('name')->get(),
        );
    }

    /**
     * @var \Illuminate\Support\Collection<int, TmdbMovie>
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

    final public function filters(): TorrentSearchFiltersDTO
    {
        return (new TorrentSearchFiltersDTO(
            name: $this->name,
            description: $this->description,
            mediainfo: $this->mediainfo,
            uploader: $this->uploader,
            keywords: $this->keywords ? array_map('trim', explode(',', $this->keywords)) : [],
            startYear: $this->startYear,
            endYear: $this->endYear,
            minSize: $this->minSize === null ? null : $this->minSize * $this->minSizeMultiplier,
            maxSize: $this->maxSize === null ? null : $this->maxSize * $this->maxSizeMultiplier,
            episodeNumber: $this->episodeNumber,
            seasonNumber: $this->seasonNumber,
            categoryIds: $this->categoryIds,
            typeIds: $this->typeIds,
            resolutionIds: $this->resolutionIds,
            genreIds: $this->genreIds,
            regionIds: $this->regionIds,
            distributorIds: $this->distributorIds,
            adult: match (true) {
                $this->adult === 'include'                                                       => true,
                $this->adult === 'exclude'                                                       => false,
                $this->adult === 'any' && auth()->user()->settings->show_adult_content === false => false,
                default                                                                          => null,
            },
            tmdbId: $this->tmdbId,
            imdbId: $this->imdbId === '' ? null : ((int) (preg_match('/tt0*(\d{7,})/', $this->imdbId, $matches) ? $matches[1] : $this->imdbId)),
            tvdbId: $this->tvdbId,
            malId: $this->malId,
            playlistId: $this->playlistId,
            collectionId: $this->collectionId,
            networkId: $this->networkId,
            companyId: $this->companyId,
            primaryLanguageNames: $this->primaryLanguageNames,
            free: $this->free,
            doubleup: $this->doubleup,
            featured: $this->featured,
            refundable: $this->refundable,
            highspeed: $this->highspeed,
            internal: $this->internal,
            trumpable: $this->trumpable,
            personalRelease: $this->personalRelease,
            alive: $this->alive,
            dying: $this->dying,
            dead: $this->dead,
            graveyard: $this->graveyard,
            userBookmarked: $this->bookmarked,
            userWished: $this->wished,
            userDownloaded: match (true) {
                $this->downloaded    => true,
                $this->notDownloaded => false,
                default              => null,
            },
            userSeeder: match (true) {
                $this->seeding                     => true,
                $this->leeching, $this->incomplete => false,
                default                            => null,
            },
            userActive: match (true) {
                $this->seeding    => true,
                $this->leeching   => true,
                $this->incomplete => false,
                default           => null,
            },
            musicArtist: $this->musicArtist === '' ? null : $this->musicArtist,
            musicLabel: $this->musicLabel === '' ? null : $this->musicLabel,
            musicFormat: $this->musicFormat === '' ? null : $this->musicFormat,
            musicYear: $this->musicYear,
            gamePlatform: $this->gamePlatform === '' ? null : $this->gamePlatform,
            gameGenre: $this->gameGenre === '' ? null : $this->gameGenre,
            gameDeveloper: $this->gameDeveloper === '' ? null : $this->gameDeveloper,
            bookAuthor: $this->bookAuthor === '' ? null : $this->bookAuthor,
            bookLanguage: $this->bookLanguage === '' ? null : $this->bookLanguage,
            bookPublisher: $this->bookPublisher === '' ? null : $this->bookPublisher,
        ));
    }

    /**
     * One row per catalogue Work (see CONTEXT.md: Work -> Edition -> Variant
     * -> Torrent) across every category, not only movies/TV, and not
     * requiring an IMDb id: this powers the 'list', 'card' and 'group'
     * layouts alike so the same title never appears as several duplicate
     * entries differing only by quality. Pagination counts matching Works,
     * not raw torrent rows; every Work carries a bounded, eager-loaded
     * sample of its own accessible variants (quality/edition/scope), with a
     * link to the full, separately paginated list on the Work page for
     * titles with more variants than the bound.
     *
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, MediaWork>
     */
    #[Computed]
    final protected function workGroups(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $user = auth()->user();

        // Whitelist which columns are allowed to be ordered by: these are
        // aggregates across every variant of a Work, not a single torrent.
        if (!\in_array($this->sortField, [
            'bumped_at',
            'created_at',
            'times_completed',
        ])) {
            $this->reset('sortField');
        }

        $isSqlAllowed = (($user->group->is_modo || $user->group->is_torrent_modo || $user->group->is_editor) && $this->driver === 'sql') || $this->description || $this->mediainfo;

        $groups = $this->paginatedWorkGroups(
            fn () => Torrent::query()
                ->select('media_work_id')
                ->selectRaw('MAX(sticky) as sticky')
                ->selectRaw('MAX(bumped_at) as bumped_at')
                ->selectRaw('MAX(created_at) as created_at')
                ->selectRaw('SUM(times_completed) as times_completed')
                ->selectRaw('COUNT(*) as variant_count')
                ->whereNotNull('media_work_id')
                ->groupBy('media_work_id'),
            $isSqlAllowed,
        );

        $workIds = $groups->getCollection()->pluck('media_work_id');

        if ($workIds->isEmpty()) {
            $this->displayedTorrentIds = [];

            return $groups;
        }

        $works = MediaWork::query()->whereIntegerInRaw('id', $workIds)->get()->keyBy('id');

        // Bounded eager load: at most this many variants render inline
        // per Work; the rest remain reachable, paginated, on works.show.
        $perWorkCap = 20;

        $eagerLoads = fn (Builder $query) => $query
            ->with([
                'type:id,name,position',
                'resolution:id,name,position',
                'category:id,name,position',
                'user:id,username,group_id',
            ])
            ->withCount(['comments'])
            ->when(
                !config('announce.external_tracker.is_enabled'),
                fn ($query) => $query->withCount([
                    'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                    'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                ]),
            )
            ->withExists([
                'featured as featured',
                'freeleechTokens'    => fn ($query) => $query->where('user_id', '=', $user->id),
                'bookmarks'          => fn ($query) => $query->where('user_id', '=', $user->id),
                'history as seeding' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 1),
                'history as leeching' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 1)
                    ->where('seeder', '=', 0),
                'history as completed' => fn ($query) => $query->where('user_id', '=', $user->id)
                    ->where('active', '=', 0)
                    ->where('seeder', '=', 1),
                'trump',
            ]);

        if ($isSqlAllowed) {
            $predicate = $this->filters()->toSqlQueryBuilder();
            $works->load(['torrents' => function ($relation) use ($predicate, $eagerLoads, $perWorkCap): void {
                $eagerLoads($relation->getQuery());
                $relation->where($predicate)
                    ->orderBy('season_number')
                    ->orderBy('episode_number')
                    ->orderBy('id')
                    ->limit($perWorkCap);
            }]);
            $torrents = $works->flatMap(fn (MediaWork $work) => $work->torrents);
        } else {
            $filters = $this->filters()->toMeilisearchFilter();
            $queries = $workIds->map(fn ($id) => (new SearchQuery())
                ->setIndexUid(config('scout.prefix').'torrents')
                ->setQuery($this->name)
                ->setSort(['sticky:desc', $this->sortField.':'.$this->sortDirection])
                ->setFilter([...$filters, 'media_work_id = '.(int) $id])
                ->setMatchingStrategy('all')
                ->setLimit($perWorkCap)
                ->setAttributesToRetrieve(['id']))->all();
            $batch = (new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key')))->multiSearch($queries);
            $torrentIds = [];

            foreach ($batch['results'] as $result) {
                foreach ($result['hits'] as $hit) {
                    $torrentIds[] = $hit['id'];
                }
            }

            $torrents = Torrent::query()->whereIntegerInRaw('id', $torrentIds);
            $eagerLoads($torrents);
            $torrents = $torrents->get();
        }

        // Candidate ids for the periodic live-stats poll (see
        // refreshDisplayedStats()); re-validated against current
        // moderation/soft-delete status on every poll, never trusted as-is.
        $this->displayedTorrentIds = $torrents->pluck('id')->all();

        $variantsByWork = $torrents->groupBy('media_work_id')->map(
            fn ($groupTorrents) => $groupTorrents
                ->sortBy(fn ($torrent) => [
                    (int) $torrent->getAttributeValue('season_number'),
                    (int) $torrent->getAttributeValue('episode_number'),
                    $torrent->getRelationValue('resolution')?->getAttributeValue('position') ?? 0,
                ])
                ->take($perWorkCap)
                ->values()
        );

        return $groups->through(function ($group) use ($works, $variantsByWork) {
            $work = $works->get($group->media_work_id);

            if ($work === null) {
                return;
            }

            $work = clone $work;
            $variants = $variantsByWork->get($group->media_work_id, collect());
            $work->setAttribute('year', $work->displayYear());
            $work->setAttribute('variantCount', (int) $group->variant_count);
            $work->setAttribute('variantScopes', $this->groupVariantsByScope($variants, $work->kind));
            $work->setRelation('torrents', $variants);

            return $work;
        });
    }

    /**
     * Poster-grid equivalent of {@see $workGroups}: one tile per Work, no
     * inline variant rows (the tile links to works.show for those).
     *
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, MediaWork>
     */
    #[Computed]
    final protected function workPosters(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // Whitelist which columns are allowed to be ordered by
        if (!\in_array($this->sortField, [
            'bumped_at',
            'times_completed',
        ])) {
            $this->reset('sortField');
        }

        $user = auth()->user();
        $isSqlAllowed = (($user->group->is_modo || $user->group->is_torrent_modo || $user->group->is_editor) && $this->driver === 'sql') || $this->description || $this->mediainfo;

        $groups = $this->paginatedWorkGroups(
            fn () => Torrent::query()
                ->select('media_work_id')
                ->selectRaw('MAX(sticky) as sticky')
                ->selectRaw('MAX(bumped_at) as bumped_at')
                ->selectRaw('SUM(times_completed) as times_completed')
                ->whereNotNull('media_work_id')
                ->groupBy('media_work_id'),
            $isSqlAllowed,
        );

        $workIds = $groups->getCollection()->pluck('media_work_id');
        $works = MediaWork::query()->whereIntegerInRaw('id', $workIds)->get()->keyBy('id');

        // The poster layout shows no live per-torrent stats, so there is
        // nothing for refreshDisplayedStats() to refresh on this layout.
        $this->displayedTorrentIds = [];

        return $groups->through(function ($group) use ($works) {
            $work = $works->get($group->media_work_id);

            if ($work === null) {
                return;
            }

            $work = clone $work;
            $work->setAttribute('year', $work->displayYear());

            return $work;
        });
    }

    /**
     * Unified accessor for whichever paginator the current $view needs;
     * the results island uses this so it only depends on one computed
     * property regardless of layout.
     */
    #[Computed]
    final protected function torrents(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return match ($this->view) {
            'poster' => $this->workPosters,
            default  => $this->workGroups,
        };
    }

    /**
     * Resolves one page of distinct `media_work_id` aggregate rows, either
     * directly in SQL or (the common case for regular users) via a
     * Meilisearch `distinct` search so filters/privacy/moderation apply
     * before grouping and pagination counts Works, not raw torrent rows.
     *
     * @param Closure(): Builder<Torrent> $groupQuery a fresh, unfiltered/unsorted aggregate query
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, object{media_work_id: int}>
     */
    private function paginatedWorkGroups(Closure $groupQuery, bool $isSqlAllowed): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if ($isSqlAllowed) {
            return $groupQuery()
                ->where($this->filters()->toSqlQueryBuilder())
                ->latest('sticky')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(min($this->perPage, 100));
        }

        $results = (new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key')))
            ->index(config('scout.prefix').'torrents')
            ->search($this->name, [
                'sort'                 => ['sticky:desc', $this->sortField.':'.$this->sortDirection],
                'filter'               => [...$this->filters()->toMeilisearchFilter(), 'media_work_id IS NOT NULL'],
                'matchingStrategy'     => 'all',
                'page'                 => (int) $this->getPage(),
                'hitsPerPage'          => min($this->perPage, 100),
                'attributesToRetrieve' => ['media_work_id'],
                'distinct'             => 'media_work_id',
            ]);

        $ids = array_column($results->getHits(), 'media_work_id');

        $groups = $groupQuery()
            ->whereIntegerInRaw('media_work_id', $ids)
            ->get()
            ->sortBy(fn ($group) => array_search($group->media_work_id, $ids));

        return new LengthAwarePaginator($groups, $results->getTotalHits(), $this->perPage, $this->getPage());
    }

    /**
     * Nests a Work's bounded variant sample as scope label (season/episode
     * for tv, a single implicit scope otherwise) -> torrent type -> torrents,
     * so quality variants group under their type and, for tv, under their
     * content scope (CONTEXT.md: a different scope is different content,
     * not merely different quality).
     *
     * @param \Illuminate\Support\Collection<int, Torrent> $torrents
     *
     * @return array<string, array<string, list<Torrent>>>
     */
    private function groupVariantsByScope(\Illuminate\Support\Collection $torrents, string $kind): array
    {
        $scopes = [];

        foreach ($torrents as $torrent) {
            $scopeLabel = $this->scopeLabelFor($torrent, $kind);
            $typeName = (string) $torrent->getRelationValue('type')?->getAttributeValue('name');
            $scopes[$scopeLabel][$typeName][] = $torrent;
        }

        return $scopes;
    }

    private function scopeLabelFor(Torrent $torrent, string $kind): string
    {
        if ($kind !== 'tv') {
            return '';
        }

        $season = (int) $torrent->getAttributeValue('season_number');
        $episode = (int) $torrent->getAttributeValue('episode_number');

        return match (true) {
            $season === 0 && $episode === 0 => __('livewire-interface.complete-pack'),
            $season === 0                   => __('catalog.scope.special', ['episode' => $episode]),
            $episode === 0                  => __('catalog.scope.season', ['season' => $season]),
            default                         => __('catalog.scope.season-episode', ['season' => $season, 'episode' => $episode]),
        };
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-search', [
            'categories'       => $this->categories,
            'types'            => $this->types,
            'resolutions'      => $this->resolutions,
            'genres'           => $this->genres,
            'primaryLanguages' => $this->primaryLanguages,
            'regions'          => $this->regions,
            'distributors'     => $this->distributors,
            'user'             => auth()->user()->load('group'),
            // $torrents/$torrentHealth/$personalFreeleech are not passed here:
            // the results section is an island (see torrent-search.blade.php)
            // and islands only see $this and public/computed properties, not
            // this view's local data array, so it reads them as
            // $this->torrents / $this->torrentHealth / $this->personalFreeleech.
        ]);
    }
}
