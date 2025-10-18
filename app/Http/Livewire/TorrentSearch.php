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
use App\Models\Category;
use App\Models\Distributor;
use App\Models\TmdbGenre;
use App\Models\TmdbMovie;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\TmdbTv;
use App\Models\Type;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use App\Traits\TorrentMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Meilisearch\Client;
use Illuminate\Support\Facades\DB;

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
     * Get torrent health statistics.
     */
    final protected object $torrentHealth {
        get => cache()->flexible(
            'torrent-search:health',
            [3600, 3600 * 2],
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

    final protected bool $personalFreeleech {
        get => cache()->get('personal_freeleech:'.auth()->id()) ?? false;
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
                $this->seeding => true,
                $this->leeching, $this->incomplete => false,
                default => null,
            },
            userActive: match (true) {
                $this->seeding    => true,
                $this->leeching   => true,
                $this->incomplete => false,
                default           => null,
            },
        ));
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Torrent>
     */
    final protected \Illuminate\Contracts\Pagination\LengthAwarePaginator $torrents {
        get {
            $user = auth()->user()->load('group');

            // Whitelist which columns are allowed to be ordered by
            if (!\in_array($this->sortField, [
                'name',
                'rating',
                'size',
                'seeders',
                'leechers',
                'times_completed',
                'created_at',
                'bumped_at'
            ])) {
                $this->reset('sortField');
            }

            $isSqlAllowed = (($user->group->is_modo || $user->group->is_torrent_modo || $user->group->is_editor) && $this->driver === 'sql') || $this->description || $this->mediainfo;

            $eagerLoads = fn (Builder $query) => $query
                ->with(['user:id,username,group_id', 'user.group', 'category', 'type', 'resolution'])
                ->withCount([
                    'comments',
                    'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                    'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                ])
                ->withExists([
                    'featured as featured',
                    'bookmarks'          => fn ($query) => $query->where('user_id', '=', $user->id),
                    'freeleechTokens'    => fn ($query) => $query->where('user_id', '=', $user->id),
                    'history as seeding' => fn ($query) => $query->where('user_id', '=', $user->id)
                        ->where('active', '=', 1)
                        ->where('seeder', '=', 1),
                    'history as leeching' => fn ($query) => $query->where('user_id', '=', $user->id)
                        ->where('active', '=', 1)
                        ->where('seeder', '=', 0),
                    'history as completed' => fn ($query) => $query->where('user_id', '=', $user->id)
                        ->where('active', '=', 0)
                        ->where('seeder', '=', 0),
                    'trump',
                ])
                ->selectRaw(<<<'SQL'
                CASE
                    WHEN category_id IN (SELECT id FROM categories WHERE movie_meta = 1) THEN 'movie'
                    WHEN category_id IN (SELECT id FROM categories WHERE tv_meta = 1) THEN 'tv'
                    WHEN category_id IN (SELECT id FROM categories WHERE game_meta = 1) THEN 'game'
                    WHEN category_id IN (SELECT id FROM categories WHERE music_meta = 1) THEN 'music'
                    WHEN category_id IN (SELECT id FROM categories WHERE no_meta = 1) THEN 'no'
                END AS meta
            SQL);

            if ($isSqlAllowed) {
                $torrents = Torrent::query()
                    ->where($this->filters()->toSqlQueryBuilder())
                    ->latest('sticky')
                    ->orderBy($this->sortField, $this->sortDirection);

                $eagerLoads($torrents);
                $torrents = $torrents->paginate(min($this->perPage, 100));
            } else {
                $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
                $index = $client->getIndex(config('scout.prefix').'torrents');

                $results = $index->search($this->name, [
                    'sort' => [
                        'sticky:desc',
                        $this->sortField.':'.$this->sortDirection,
                    ],
                    'filter'               => $this->filters()->toMeilisearchFilter(),
                    'matchingStrategy'     => 'all',
                    'page'                 => (int) $this->getPage(),
                    'hitsPerPage'          => min($this->perPage, 100),
                    'attributesToRetrieve' => ['id'],
                ]);

                $ids = array_column($results->getHits(), 'id');

                $torrents = Torrent::query()->whereIntegerInRaw('id', $ids);

                $eagerLoads($torrents);

                $torrents = $torrents->get()->sortBy(fn ($torrent) => array_search($torrent->id, $ids));

                $torrents = new LengthAwarePaginator($torrents, $results->getTotalHits(), $this->perPage, $this->getPage());
            }

            // See app/Traits/TorrentMeta.php
            $this->scopeMeta($torrents, withCredits: true);

            return $torrents;
        }
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Torrent>
     */
    final protected $groupedTorrents {
        get {
            $user = auth()->user();

            // Whitelist which columns are allowed to be ordered by
            if (!\in_array($this->sortField, [
                'bumped_at',
                'created_at',
                'times_completed',
            ])) {
                $this->reset('sortField');
            }

            $isSqlAllowed = (($user->group->is_modo || $user->group->is_torrent_modo || $user->group->is_editor) && $this->driver === 'sql') || $this->description || $this->mediainfo;

            $groupQuery = Torrent::query()
                ->select('tmdb_movie_id', 'tmdb_tv_id')
                ->selectRaw('MAX(sticky) as sticky')
                ->selectRaw('MAX(bumped_at) as bumped_at')
                ->selectRaw('MAX(created_at) as created_at')
                ->selectRaw('SUM(times_completed) as times_completed')
                ->selectRaw(<<<'SQL'
                MIN(CASE
                    WHEN category_id IN (SELECT id FROM categories WHERE movie_meta = 1) THEN 'movie'
                    WHEN category_id IN (SELECT id FROM categories WHERE tv_meta = 1) THEN 'tv'
                END) AS meta
            SQL)
                ->havingNotNull('meta')
                ->where(fn ($query) => $query->whereNotNull('tmdb_movie_id')->orWhereNotNull('tmdb_tv_id'))
                ->whereNotNull('imdb')
                ->groupBy('tmdb_movie_id', 'tmdb_tv_id')
                ->latest('sticky')
                ->orderBy($this->sortField, $this->sortDirection);

            $eagerLoads = fn (Builder $query) => $query
                ->with([
                    'type:id,name,position',
                    'resolution:id,name,position',
                    'category:id,name,position',
                    'user:id,username,group_id',
                ])
                ->select([
                    'id',
                    'name',
                    'info_hash',
                    'size',
                    'leechers',
                    'seeders',
                    'times_completed',
                    'category_id',
                    'user_id',
                    'season_number',
                    'episode_number',
                    'tmdb_movie_id',
                    'tmdb_tv_id',
                    'free',
                    'doubleup',
                    'highspeed',
                    'sticky',
                    'internal',
                    'created_at',
                    'bumped_at',
                    'type_id',
                    'resolution_id',
                    'personal_release',
                ])
                ->selectRaw(<<<'SQL'
                CASE
                    WHEN category_id IN (SELECT id FROM categories WHERE movie_meta = 1) THEN 'movie'
                    WHEN category_id IN (SELECT id FROM categories WHERE tv_meta = 1) THEN 'tv'
                END AS meta
            SQL)
                ->withCount([
                    'comments',
                ])
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
                $groups = $groupQuery
                    ->where($this->filters()->toSqlQueryBuilder())
                    ->paginate(min($this->perPage, 100));
            } else {
                $results = (new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key')))
                    ->index(config('scout.prefix').'torrents')
                    ->search($this->name, [
                        'sort'                 => ['sticky:desc', $this->sortField.':'.$this->sortDirection,],
                        'filter'               => [...$this->filters()->toMeilisearchFilter(), 'imdb IS NOT NULL', ['tmdb_movie_id IS NOT NULL', 'tmdb_tv_id IS NOT NULL']],
                        'matchingStrategy'     => 'all',
                        'page'                 => (int) $this->getPage(),
                        'hitsPerPage'          => min($this->perPage, 100),
                        'attributesToRetrieve' => ['tmdb_movie_id', 'tmdb_tv_id'],
                        'distinct'             => 'imdb',
                    ]);

                $ids = [];

                foreach ($results->getHits() as $result) {
                    if ($result['tmdb_movie_id']) {
                        $ids[] = "tmdb-movie:{$result['tmdb_movie_id']}";
                    } elseif ($result['tmdb_tv_id']) {
                        $ids[] = "tmdb-tv:{$result['tmdb_tv_id']}";
                    }
                }

                $groups = $groupQuery
                    ->where(
                        fn ($query) => $query
                            ->whereIntegerInRaw('tmdb_movie_id', array_filter(array_column($results->getHits(), 'tmdb_movie_id')))
                            ->orWhereIntegerInRaw('tmdb_tv_id', array_filter(array_column($results->getHits(), 'tmdb_tv_id')))
                    )
                    ->get()
                    ->sortBy(fn ($group) => array_search($group->tmdb_movie_id ? "tmdb-movie:{$group->tmdb_movie_id}" : "tmdb-tv:{$group->tmdb_tv_id}", $ids));

                $groups = new LengthAwarePaginator($groups, $results->getTotalHits(), $this->perPage, $this->getPage());
            }

            $movieIds = $groups->getCollection()->where('meta', '=', 'movie')->pluck('tmdb_movie_id');
            $tvIds = $groups->getCollection()->where('meta', '=', 'tv')->pluck('tmdb_tv_id');

            $movies = TmdbMovie::with('genres', 'directors')->whereIntegerInRaw('id', $movieIds)->get()->keyBy('id');
            $tv = TmdbTv::with('genres', 'creators')->whereIntegerInRaw('id', $tvIds)->get()->keyBy('id');

            if ($isSqlAllowed) {
                $torrents = Torrent::query()
                    ->where(
                        fn ($query) => $query
                            ->where(
                                fn ($query) => $query
                                    ->whereRelation('category', 'movie_meta', '=', true)
                                    ->whereIntegerInRaw('tmdb_movie_id', $movieIds)
                            )
                            ->orWhere(
                                fn ($query) => $query
                                    ->whereRelation('category', 'tv_meta', '=', true)
                                    ->whereIntegerInRaw('tmdb_tv_id', $tvIds)
                            )
                    )
                    ->where($this->filters()->toSqlQueryBuilder());

                $eagerLoads($torrents);

                $torrents = $torrents->get();
            } else {
                $results = (new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key')))
                    ->index(config('scout.prefix').'torrents')
                    ->search($this->name, [
                        'sort'   => ['sticky:desc', $this->sortField.':'.$this->sortDirection,],
                        'filter' => [
                            ...$this->filters()->toMeilisearchFilter(),
                            [
                                'tmdb_movie_id IN '.json_encode($movieIds->values()),
                                'tmdb_tv_id IN '.json_encode($tvIds->values())
                            ]
                        ],
                        'matchingStrategy'     => 'all',
                        'hitsPerPage'          => 10_000,
                        'attributesToRetrieve' => ['id'],
                    ]);

                $torrentIds = array_column($results->getHits(), 'id');

                $torrents = Torrent::query()->whereIntegerInRaw('id', $torrentIds);

                $eagerLoads($torrents);

                $torrents = $torrents->get();
            }

            $groupedTorrents = [];

            foreach ($torrents as &$torrent) {
                // Memoizing and avoiding casts reduces runtime duration from 70ms to 40ms.
                // If accessing laravel's attributes array directly, it's reduced to 11ms,
                // but the attributes array is marked as protected so we can't access it.
                $tmdb = $torrent->getAttributeValue('tmdb_movie_id') ?: $torrent->getAttributeValue('tmdb_tv_id');
                $type = $torrent->getRelationValue('type')->getAttributeValue('name');

                switch ($torrent->getAttributeValue('meta')) {
                    case 'movie':
                        $groupedTorrents['movie'][$tmdb]['Movie'][$type][] = $torrent;
                        $groupedTorrents['movie'][$tmdb]['category_id'] = $torrent->getAttributeValue('category_id');

                        break;
                    case 'tv':
                        $episode = $torrent->getAttributeValue('episode_number');
                        $season = $torrent->getAttributeValue('season_number');

                        if ($season == 0) {
                            if ($episode == 0) {
                                $groupedTorrents['tv'][$tmdb]['Complete Pack'][$type][] = $torrent;
                            } else {
                                $groupedTorrents['tv'][$tmdb]['Specials']["Special {$episode}"][$type][] = $torrent;
                            }
                        } else {
                            if ($episode == 0) {
                                $groupedTorrents['tv'][$tmdb]['Seasons']["Season {$season}"]['Season Pack'][$type][] = $torrent;
                            } else {
                                $groupedTorrents['tv'][$tmdb]['Seasons']["Season {$season}"]['Episodes']["Episode {$episode}"][$type][] = $torrent;
                            }
                        }

                        $groupedTorrents['tv'][$tmdb]['category_id'] = $torrent->getAttributeValue('category_id');
                }
            }

            foreach ($groupedTorrents as $mediaType => &$workTorrents) {
                switch ($mediaType) {
                    case 'movie':
                        foreach ($workTorrents as &$movieTorrents) {
                            $this->sortTorrentTypes($movieTorrents['Movie']);
                        }

                        break;
                    case 'tv':
                        foreach ($workTorrents as &$tvTorrents) {
                            foreach ($tvTorrents as $packOrSpecialOrSeasonsType => &$packOrSpecialOrSeasons) {
                                switch ($packOrSpecialOrSeasonsType) {
                                    case 'Complete Pack':
                                        $this->sortTorrentTypes($packOrSpecialOrSeasons);

                                        break;
                                    case 'Specials':
                                        krsort($packOrSpecialOrSeasons, SORT_NATURAL);

                                        foreach ($packOrSpecialOrSeasons as &$specialTorrents) {
                                            $this->sortTorrentTypes($specialTorrents);
                                        }

                                        break;
                                    case 'Seasons':
                                        krsort($packOrSpecialOrSeasons, SORT_NATURAL);

                                        foreach ($packOrSpecialOrSeasons as &$season) {
                                            foreach ($season as $packOrEpisodesType => &$packOrEpisodes) {
                                                switch ($packOrEpisodesType) {
                                                    case 'Season Pack':
                                                        $this->sortTorrentTypes($packOrEpisodes);

                                                        break;
                                                    case 'Episodes':
                                                        krsort($packOrEpisodes, SORT_NATURAL);

                                                        foreach ($packOrEpisodes as &$episodeTorrents) {
                                                            $this->sortTorrentTypes($episodeTorrents);
                                                        }

                                                        break;
                                                }
                                            }
                                        }
                                }
                            }
                        }
                }
            }

            $medias = $groups->through(function ($group) use ($groupedTorrents, $movies, $tv) {
                switch ($group->meta) {
                    case 'movie':
                        if ($movies->has($group->tmdb_movie_id)) {
                            $media = $movies[$group->tmdb_movie_id];
                            $media->setAttribute('meta', 'movie');
                            $media->setRelation('torrents', $groupedTorrents['movie'][$group->tmdb_movie_id] ?? []);
                            $media->setAttribute('category_id', $media->torrents['category_id']);
                        } else {
                            $media = null;
                        }

                        break;
                    case 'tv':
                        if ($tv->has($group->tmdb_tv_id)) {
                            $media = $tv[$group->tmdb_tv_id];
                            $media->setAttribute('meta', 'tv');
                            $media->setRelation('torrents', $groupedTorrents['tv'][$group->tmdb_tv_id] ?? []);
                            $media->setAttribute('category_id', $media->torrents['category_id']);
                        } else {
                            $media = null;
                        }

                        break;
                    default:
                        $media = null;
                }

                return $media;
            });

            return $medias;
        }
    }

    /**
     * @param array<Torrent> $torrentTypeTorrents
     */
    private function sortTorrentTypes(&$torrentTypeTorrents): void
    {
        uasort(
            $torrentTypeTorrents,
            fn ($a, $b) => $a[0]->getRelationValue('type')->getAttributeValue('position')
                <=> $b[0]->getRelationValue('type')->getAttributeValue('position')
        );

        foreach ($torrentTypeTorrents as &$torrents) {
            usort(
                $torrents,
                fn ($a, $b) => [
                    $a->getRelationValue('resolution')->getAttributeValue('position'),
                    $a->getAttributeValue('name')
                ] <=> [
                    $b->getRelationValue('resolution')->getAttributeValue('position'),
                    $b->getAttributeValue('name')
                ]
            );
        }
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Torrent>
     */
    final protected $groupedPosters {
        get {
            // Whitelist which columns are allowed to be ordered by
            if (!\in_array($this->sortField, [
                'bumped_at',
                'times_completed',
            ])) {
                $this->reset('sortField');
            }

            $groups = Torrent::query()
                ->select('tmdb_movie_id', 'tmdb_tv_id')
                ->selectRaw('MAX(sticky) as sticky')
                ->selectRaw('MAX(bumped_at) as bumped_at')
                ->selectRaw('SUM(times_completed) as times_completed')
                ->selectRaw('MIN(category_id) as category_id')
                ->selectRaw(<<<'SQL'
                MIN(CASE
                    WHEN category_id IN (SELECT id FROM categories WHERE movie_meta = 1) THEN 'movie'
                    WHEN category_id IN (SELECT id FROM categories WHERE tv_meta = 1) THEN 'tv'
                END) AS meta
            SQL)
                ->havingNotNull('meta')
                ->where(fn ($query) => $query->whereNotNull('tmdb_movie_id')->orWhereNotNull('tmdb_tv_id'))
                ->where($this->filters()->toSqlQueryBuilder())
                ->groupBy('tmdb_movie_id', 'tmdb_tv_id')
                ->latest('sticky')
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(min($this->perPage, 100));

            $movieIds = $groups->getCollection()->where('meta', '=', 'movie')->pluck('tmdb_movie_id');
            $tvIds = $groups->getCollection()->where('meta', '=', 'tv')->pluck('tmdb_tv_id');

            $movies = TmdbMovie::with('genres', 'directors')->whereIntegerInRaw('id', $movieIds)->get()->keyBy('id');
            $tv = TmdbTv::with('genres', 'creators')->whereIntegerInRaw('id', $tvIds)->get()->keyBy('id');

            $groups = $groups->through(function ($group) use ($movies, $tv) {
                switch ($group->meta) {
                    case 'movie':
                        $group->movie = $movies[$group->tmdb_movie_id] ?? null;

                        break;
                    case 'tv':
                        $group->tv = $tv[$group->tmdb_tv_id] ?? null;

                        break;
                }

                return $group;
            });

            return $groups;
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-search', [
            'categories'        => $this->categories,
            'types'             => $this->types,
            'resolutions'       => $this->resolutions,
            'genres'            => $this->genres,
            'primaryLanguages'  => $this->primaryLanguages,
            'regions'           => $this->regions,
            'distributors'      => $this->distributors,
            'user'              => auth()->user()->load('group'),
            'personalFreeleech' => $this->personalFreeleech,
            'torrents'          => match ($this->view) {
                'group'  => $this->groupedTorrents,
                'poster' => $this->groupedPosters,
                default  => $this->torrents,
            },
            'torrentHealth' => $this->torrentHealth,
        ]);
    }
}
