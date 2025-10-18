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

namespace App\Http\Controllers\API;

use App\DTO\TorrentSearchFiltersDTO;
use App\Enums\AuthGuard;
use App\Helpers\Bencode;
use App\Helpers\TorrentHelper;
use App\Helpers\TorrentTools;
use App\Http\Resources\TorrentResource;
use App\Http\Resources\TorrentsResource;
use App\Models\Category;
use App\Models\FeaturedTorrent;
use App\Models\IgdbGame;
use App\Models\Keyword;
use App\Models\TmdbMovie;
use App\Models\Torrent;
use App\Models\TorrentFile;
use App\Models\TmdbTv;
use App\Models\User;
use App\Repositories\ChatRepository;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use App\Services\Unit3dAnnounce;
use App\Traits\TorrentMeta;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Meilisearch\Endpoints\Indexes;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TorrentControllerTest
 */
class TorrentController extends BaseController
{
    use TorrentMeta;

    public int $perPage = 25;

    public string $sortField = 'bumped_at';

    public string $sortDirection = 'desc';

    /**
     * TorrentController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): TorrentsResource
    {
        $torrents = cache()->flexible('torrent-api-index', [60 * 5, 60 * 6], function () {
            $torrents = Torrent::with(
                ['user:id,username', 'category', 'type', 'resolution', 'region', 'distributor', 'files']
            )
                ->withExists([
                    'featured as featured'
                ])
                ->select('*')
                ->selectRaw(
                    "
                CASE
                    WHEN category_id IN (SELECT `id` from `categories` where `movie_meta` = 1) THEN 'movie'
                    WHEN category_id IN (SELECT `id` from `categories` where `tv_meta` = 1) THEN 'tv'
                    WHEN category_id IN (SELECT `id` from `categories` where `game_meta` = 1) THEN 'game'
                    WHEN category_id IN (SELECT `id` from `categories` where `music_meta` = 1) THEN 'music'
                    WHEN category_id IN (SELECT `id` from `categories` where `no_meta` = 1) THEN 'no'
                END as meta
            "
                )
                ->latest('sticky')
                ->latest('bumped_at')
                ->cursorPaginate(25);

            // See app/Traits/TorrentMeta.php
            $this->scopeMeta($torrents);

            return $torrents;
        });

        return new TorrentsResource($torrents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user()->loadExists('internals');
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        $requestFile = $request->file('torrent');

        if (!$request->hasFile('torrent')) {
            return $this->sendError('Validation Error.', 'You Must Provide A Torrent File For Upload!');
        }

        if ($requestFile->getError() !== 0 || $requestFile->getClientOriginalExtension() !== 'torrent') {
            return $this->sendError('Validation Error.', 'You Must Provide A Valid Torrent File For Upload!');
        }

        // Move and decode the torrent temporarily
        $decodedTorrent = TorrentTools::normalizeTorrent($requestFile);
        $infohash = Bencode::get_infohash($decodedTorrent);

        try {
            $meta = Bencode::get_meta($decodedTorrent);
        } catch (Exception) {
            return $this->sendError('Validation Error.', 'You Must Provide A Valid Torrent File For Upload!');
        }

        foreach (TorrentTools::getFilenameArray($decodedTorrent) as $name) {
            if (!TorrentTools::isValidFilename($name)) {
                return $this->sendError('Validation Error.', 'Invalid Filenames In Torrent Files!');
            }
        }

        $fileName = \sprintf('%s.torrent', uniqid('', true)); // Generate a unique name
        Storage::disk('torrent-files')->put($fileName, Bencode::bencode($decodedTorrent));

        // Find the right category
        $category = Category::withCount('torrents')->findOrFail($request->integer('category_id'));

        // Create the torrent (DB)
        $torrent = app()->make(Torrent::class);
        $torrent->name = $request->input('name');
        $torrent->description = $request->input('description');
        $torrent->mediainfo = TorrentTools::anonymizeMediainfo($request->filled('mediainfo') ? $request->string('mediainfo') : null);
        $torrent->bdinfo = $request->input('bdinfo');
        $torrent->info_hash = $infohash;
        $torrent->file_name = $fileName;
        $torrent->num_file = $meta['count'];
        $torrent->folder = Bencode::get_name($decodedTorrent);
        $torrent->size = $meta['size'];
        $torrent->nfo = ($request->hasFile('nfo')) ? TorrentTools::getNfo($request->file('nfo')) : '';
        $torrent->category_id = $category->id;
        $torrent->type_id = $request->input('type_id');
        $torrent->resolution_id = $request->input('resolution_id');
        $torrent->region_id = $request->input('region_id');
        $torrent->distributor_id = $request->input('distributor_id');
        $torrent->user_id = $user->id;
        $torrent->imdb = ($category->movie_meta || $category->tv_meta) ? ($request->integer('imdb') ?: null) : null;
        $torrent->tvdb = ($category->movie_meta || $category->tv_meta) ? ($request->integer('tvdb') ?: null) : null;
        $torrent->tmdb_movie_id = $category->movie_meta ? ($request->integer('tmdb') ?: null) : null;
        $torrent->tmdb_tv_id = $category->tv_meta ? ($request->integer('tmdb') ?: null) : null;
        $torrent->mal = ($category->movie_meta || $category->tv_meta) ? ($request->integer('mal') ?: null) : null;
        $torrent->igdb = $category->game_meta ? ($request->integer('igdb') ?: null) : null;
        $torrent->season_number = $request->input('season_number');
        $torrent->episode_number = $request->input('episode_number');
        $torrent->anon = $request->input('anonymous');
        $torrent->personal_release = $request->input('personal_release') ?? false;

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        $torrent->internal = $user->group->is_modo || $user->internals_exists ? ($request->input('internal') ?? 0) : 0;

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        $torrent->doubleup = $user->group->is_modo || $user->internals_exists ? ($request->input('doubleup') ?? 0) : 0;

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        $torrent->refundable = $user->group->is_modo || $user->internals_exists ? ($request->input('refundable') ?? false) : false;
        $du_until = $request->input('du_until');

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        if (($user->group->is_modo || $user->internals_exists) && isset($du_until)) {
            $torrent->du_until = Carbon::now()->addDays($request->integer('du_until'));
        }

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        $torrent->free = $user->group->is_modo || $user->internals_exists ? ($request->input('free') ?? 0) : 0;
        $fl_until = $request->input('fl_until');

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        if (($user->group->is_modo || $user->internals_exists) && isset($fl_until)) {
            $torrent->fl_until = Carbon::now()->addDays($request->integer('fl_until'));
        }

        /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
        $torrent->sticky = $user->group->is_modo || $user->internals_exists ? ($request->input('sticky') ?? false) : false;
        $torrent->moderated_at = Carbon::now();
        $torrent->moderated_by = User::SYSTEM_USER_ID;

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        // Validation
        $v = validator($torrent->toArray(), [
            'name' => [
                'required',
                Rule::unique('torrents')->whereNull('deleted_at'),
                'max:255',
            ],
            'description' => [
                'required',
            ],
            'info_hash' => [
                'required',
                Rule::unique('torrents')->whereNull('deleted_at'),
            ],
            'file_name' => [
                'required',
            ],
            'num_file' => [
                'required',
                'numeric',
            ],
            'size' => [
                'required',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'type_id' => [
                'required',
                'exists:types,id',
            ],
            'resolution_id' => [
                Rule::when($category->movie_meta || $category->tv_meta, 'required'),
                Rule::when(!$category->movie_meta && !$category->tv_meta, 'nullable'),
                'exists:resolutions,id',
            ],
            'region_id' => [
                'nullable',
                'exists:regions,id',
            ],
            'distributor_id' => [
                'nullable',
                'exists:distributors,id',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'imdb' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'tvdb' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_movie_id' => [
                Rule::when($category->movie_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->movie_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_tv_id' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'mal' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'igdb' => [
                Rule::when($category->game_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->game_meta, [
                    $mustBeNull,
                ]),
            ],
            'season_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                    'numeric',
                    'integer',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                    'numeric',
                    'integer',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'anon' => [
                'required',
            ],
            'personal_release' => [
                'nullable',
            ],
            'internal' => [
                'required',
            ],
            'free' => [
                'required',
                'between:0,100',
            ],
            'doubleup' => [
                'required',
            ],
            'refundable' => [
                'required',
            ],
            'sticky' => [
                'required',
            ],
        ]);

        if ($v->fails()) {
            if (Storage::disk('torrent-files')->exists($fileName)) {
                Storage::disk('torrent-files')->delete($fileName);
            }

            return $this->sendError('Validation Error.', $v->errors());
        }

        // Save The Torrent
        $torrent->save();

        // Populate the status/seeders/leechers/times_completed fields for the external tracker
        $torrent->refresh();

        // Backup the files contained in the torrent
        $files = TorrentTools::getTorrentFiles($decodedTorrent);

        foreach ($files as &$file) {
            $file['torrent_id'] = $torrent->id;
        }

        // Can't insert them all at once since some torrents have more files than mysql supports placeholders.
        // Divide by 3 since we're inserting 3 fields: name, size and torrent_id
        foreach (collect($files)->chunk(intdiv(65_000, 3)) as $files) {
            TorrentFile::insert($files->toArray());
        }

        // Set torrent to featured
        if (($user->group->is_modo || $user->group->is_internal) && $request->input('featured')) {
            $featuredTorrent = new FeaturedTorrent();
            $featuredTorrent->user_id = $user->id;
            $featuredTorrent->torrent_id = $torrent->id;
            $featuredTorrent->save();
        }

        // Tracker updates come after database updates in case tracker's offline

        Unit3dAnnounce::addTorrent($torrent);

        if (($user->group->is_modo || $user->group->is_internal) && $request->input('featured')) {
            Unit3dAnnounce::addFeaturedTorrent($torrent->id);
        }

        // Metadata updates come after tracker updates in case TMDB or IGDB is offline

        match (true) {
            $category->tv_meta && $torrent->tmdb_tv_id > 0       => new TMDBScraper()->tv($torrent->tmdb_tv_id),
            $category->movie_meta && $torrent->tmdb_movie_id > 0 => new TMDBScraper()->movie($torrent->tmdb_movie_id),
            $category->game_meta && $torrent->igdb > 0           => new IgdbScraper()->game($torrent->igdb),
            default                                              => null,
        };

        // Torrent Keywords System
        $keywords = [];

        foreach (TorrentTools::parseKeywords($request->string('keywords')) as $keyword) {
            $keywords[] = ['torrent_id' => $torrent->id, 'name' => $keyword];
        }

        foreach (collect($keywords)->chunk(intdiv(65_000, 2)) as $keywords) {
            Keyword::upsert($keywords->toArray(), ['torrent_id', 'name']);
        }

        // check for trusted user & mod queue isn't opted in and update torrent
        if ($user->group->is_trusted && !$request->boolean('mod_queue_opt_in')) {
            $appurl = config('app.url');
            $user = $torrent->user;
            $username = $user->username;
            $anon = $torrent->anon;
            $featured = ($user->group->is_modo || $user->group->is_internal) && $request->input('featured');
            $free = $torrent->free;
            $doubleup = $torrent->doubleup;

            // Announce To Shoutbox
            if (!$anon) {
                $this->chatRepository->systemMessage(
                    \sprintf('User [url=%s/users/', $appurl).$username.']'.$username.\sprintf('[/url] has uploaded a new '.$torrent->category->name.'. [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url], grab it now!'
                );
            } else {
                $this->chatRepository->systemMessage(
                    \sprintf('An anonymous user has uploaded a new '.$torrent->category->name.'. [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url], grab it now!'
                );
            }

            if ($anon && $featured == 1) {
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.'[/url] has been added to the Featured Torrents Slider by an anonymous user! Grab It While You Can!'
                );
            } elseif (!$anon && $featured == 1) {
                $this->chatRepository->systemMessage(
                    \sprintf('Ladies and Gents, [url=%s/torrents/', $appurl).$torrent->id.']'.$torrent->name.\sprintf('[/url] has been added to the Featured Torrents Slider by [url=%s/users/', $appurl).$username.']'.$username.'[/url]! Grab It While You Can!'
                );
            }

            if ($free >= 1 && $featured == 0) {
                if ($torrent->fl_until === null) {
                    $this->chatRepository->systemMessage(
                        \sprintf(
                            'Ladies and Gents, [url=%s/torrents/',
                            $appurl
                        ).$torrent->id.']'.$torrent->name.'[/url] has been granted '.$free.'% FreeLeech! Grab It While You Can!'
                    );
                } else {
                    $this->chatRepository->systemMessage(
                        \sprintf(
                            'Ladies and Gents, [url=%s/torrents/',
                            $appurl
                        ).$torrent->id.']'.$torrent->name.'[/url] has been granted '.$free.'% FreeLeech for '.$request->input('fl_until').' days.'
                    );
                }
            }

            if ($doubleup == 1 && $featured == 0) {
                if ($torrent->du_until === null) {
                    $this->chatRepository->systemMessage(
                        \sprintf(
                            'Ladies and Gents, [url=%s/torrents/',
                            $appurl
                        ).$torrent->id.']'.$torrent->name.'[/url] has been granted Double Upload! Grab It While You Can!'
                    );
                } else {
                    $this->chatRepository->systemMessage(
                        \sprintf(
                            'Ladies and Gents, [url=%s/torrents/',
                            $appurl
                        ).$torrent->id.']'.$torrent->name.'[/url] has been granted Double Upload for '.$request->input('du_until').' days.'
                    );
                }
            }

            TorrentHelper::approveHelper($torrent->id);
        }

        return $this->sendResponse(route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => auth(AuthGuard::API->value)->user()->rsskey]), 'Torrent uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): TorrentResource
    {
        $torrent = Torrent::with(['user:id,username', 'category', 'type', 'resolution', 'region', 'distributor', 'files'])
            ->select('*')
            ->selectRaw("
                CASE
                    WHEN category_id IN (SELECT `id` from `categories` where `movie_meta` = 1) THEN 'movie'
                    WHEN category_id IN (SELECT `id` from `categories` where `tv_meta` = 1) THEN 'tv'
                    WHEN category_id IN (SELECT `id` from `categories` where `game_meta` = 1) THEN 'game'
                    WHEN category_id IN (SELECT `id` from `categories` where `music_meta` = 1) THEN 'music'
                    WHEN category_id IN (SELECT `id` from `categories` where `no_meta` = 1) THEN 'no'
                END as meta
            ")
            ->findOrFail($id);

        $torrent->setAttribute('meta', null);

        if ($torrent->category->tv_meta && $torrent->tmdb_tv_id) {
            $torrent->setAttribute('meta', TmdbTv::with(['genres'])->find($torrent->tmdb_tv_id));
        }

        if ($torrent->category->movie_meta && $torrent->tmdb_movie_id) {
            $torrent->setAttribute('meta', TmdbMovie::with(['genres'])->find($torrent->tmdb_movie_id));
        }

        if ($torrent->category->game_meta && $torrent->igdb) {
            $torrent->setAttribute('meta', IgdbGame::with(['genres'])->find($torrent->igdb));
        }

        TorrentResource::withoutWrapping();

        return new TorrentResource($torrent);
    }

    /**
     * Uses Input's To Put Together A Search.
     */
    public function filter(Request $request): TorrentsResource|\Illuminate\Http\JsonResponse
    {
        $user = auth()->user()->load('group');
        $isRegexAllowed = $user->group->is_modo;
        $isSqlAllowed = ($user->group->is_modo || $user->group->is_editor) && $request->driver === 'sql';

        $request->validate([
            'sortField' => [
                'nullable',
                'sometimes',
                'in:name,size,seeders,leechers,times_completed,created_at,bumped_at',
            ],
            'sortDirection' => [
                'nullable',
                'sometimes',
                'in:asc,desc'
            ]
        ]);

        // Caching
        $url = $request->url();
        $queryParams = $request->query();

        // Don't cache the api_token so that multiple users can share the cache
        unset($queryParams['api_token']);
        $queryParams['isRegexAllowed'] = $isRegexAllowed;
        $queryParams['isSqlAllowed'] = $isSqlAllowed;

        // Sorting query params by key (acts by reference)
        ksort($queryParams);

        // Transforming the query array to query string
        $queryString = http_build_query($queryParams);
        $cacheKey = $url.'?'.$queryString;

        /** @phpstan-ignore method.unresolvableReturnType (phpstan is unable to resolve type because it's returning a phpstan-ignored line) */
        [$torrents, $hasMore] = cache()->flexible($cacheKey, [60 * 5, 60 * 6], function () use ($request, $isSqlAllowed) {
            $eagerLoads = fn (Builder $query) => $query
                ->with(['user:id,username', 'category', 'type', 'resolution', 'distributor', 'region', 'files'])
                ->select('*')
                ->selectRaw("
                    CASE
                        WHEN category_id IN (SELECT `id` from `categories` where `movie_meta` = 1) THEN 'movie'
                        WHEN category_id IN (SELECT `id` from `categories` where `tv_meta` = 1) THEN 'tv'
                        WHEN category_id IN (SELECT `id` from `categories` where `game_meta` = 1) THEN 'game'
                        WHEN category_id IN (SELECT `id` from `categories` where `music_meta` = 1) THEN 'music'
                        WHEN category_id IN (SELECT `id` from `categories` where `no_meta` = 1) THEN 'no'
                    END as meta
                ");

            $filters = new TorrentSearchFiltersDTO(
                name: $request->filled('name') ? $request->string('name')->toString() : '',
                description: $request->filled('description') ? $request->string('description')->toString() : '',
                mediainfo: $request->filled('mediainfo') ? $request->string('mediainfo')->toString() : '',
                uploader: $request->filled('uploader') ? $request->string('uploader')->toString() : '',
                keywords: $request->filled('keywords') ? array_map('trim', explode(',', $request->string('keywords')->toString())) : [],
                startYear: $request->filled('startYear') ? $request->integer('startYear') : null,
                endYear: $request->filled('endYear') ? $request->integer('endYear') : null,
                categoryIds: $request->filled('categories') ? array_map('intval', $request->categories) : [],
                typeIds: $request->filled('types') ? array_map('intval', $request->types) : [],
                resolutionIds: $request->filled('resolutions') ? array_map('intval', $request->resolutions) : [],
                genreIds: $request->filled('genres') ? array_map('intval', $request->genres) : [],
                tmdbId: $request->filled('tmdbId') ? $request->integer('tmdbId') : null,
                imdbId: $request->filled('imdbId') ? $request->integer('imdbId') : null,
                tvdbId: $request->filled('tvdbId') ? $request->integer('tvdbId') : null,
                malId: $request->filled('malId') ? $request->integer('malId') : null,
                playlistId: $request->filled('playlistId') ? $request->integer('playlistId') : null,
                collectionId: $request->filled('collectionId') ? $request->integer('collectionId') : null,
                primaryLanguageNames: $request->filled('primaryLanguages') ? array_map('str', $request->primaryLanguages) : [],
                adult: $request->filled('adult') ? $request->boolean('adult') : null,
                free: $request->filled('free') ? array_map('intval', (array) $request->free) : [],
                doubleup: $request->filled('doubleup'),
                refundable: $request->filled('refundable'),
                featured: $request->filled('featured'),
                highspeed: $request->filled('highspeed'),
                internal: $request->filled('internal'),
                personalRelease: $request->filled('personalRelease'),
                alive: $request->filled('alive'),
                dying: $request->filled('dying'),
                dead: $request->filled('dead'),
                filename: $request->filled('file_name') ? $request->string('file_name')->toString() : '',
                seasonNumber: $request->filled('seasonNumber') ? $request->integer('seasonNumber') : null,
                episodeNumber: $request->filled('episodeNumber') ? $request->integer('episodeNumber') : null,
            );

            if ($isSqlAllowed) {
                $torrents = Torrent::query()
                    ->where($filters->toSqlQueryBuilder())
                    ->latest('sticky')
                    ->orderBy($request->input('sortField') ?? $this->sortField, $request->input('sortDirection') ?? $this->sortDirection)
                    ->cursorPaginate(min($request->input('perPage') ?? $this->perPage, 100));

                // See app/Traits/TorrentMeta.php
                $this->scopeMeta($torrents);

                $hasMore = $torrents->nextCursor() !== null;
            } else {
                $paginator = Torrent::search(
                    $request->filled('name') ? $request->string('name')->toString() : '',
                    function (Indexes $meilisearch, string $query, array $options) use ($request, $filters) {
                        $options['sort'] = [
                            ($request->input('sortField') ?: $this->sortField).':'.($request->input('sortDirection') ?? $this->sortDirection),
                        ];
                        $options['filter'] = $filters->toMeilisearchFilter();
                        $options['matchingStrategy'] = 'all';

                        $results = $meilisearch->search($query, $options);

                        return $results;
                    }
                )
                    ->query($eagerLoads)
                    ->simplePaginateRaw(min($request->input('perPage') ?? $this->perPage, 100));

                $hasMore = $paginator->hasMorePages();

                /** @phpstan-ignore method.notFound (this method exists at time of writing) */
                $results = $paginator->getCollection();
                $torrents = collect();

                foreach ($results['hits'] ?? [] as $hit) {
                    $meta = $hit['tmdb_movie'] ?? $hit['tmdb_tv'] ?? [];

                    /** @see TorrentResource */
                    $torrents->push([
                        'type'       => 'torrent',
                        'id'         => (string) $hit['id'],
                        'attributes' => [
                            'meta' => [
                                'poster' => \array_key_exists('poster', $meta) ? tmdb_image('poster_small', $meta['poster']) : null,
                                'genres' => \array_key_exists('genres', $meta) ? implode(', ', array_column($meta['genres'], 'name')) : '',
                            ],
                            'name'             => $hit['name'],
                            'release_year'     => $meta['year'] ?? null,
                            'category'         => $hit['category']['name'] ?? null,
                            'type'             => $hit['type']['name'] ?? null,
                            'resolution'       => $hit['resolution']['name'] ?? null,
                            'media_info'       => $hit['mediainfo'],
                            'bd_info'          => $hit['bdinfo'],
                            'description'      => $hit['description'],
                            'size'             => $hit['size'],
                            'num_file'         => $hit['num_file'],
                            'files'            => $hit['files'],
                            'freeleech'        => $hit['free'].'%',
                            'double_upload'    => $hit['doubleup'],
                            'refundable'       => $hit['refundable'],
                            'internal'         => $hit['internal'],
                            'featured'         => $hit['featured'],
                            'personal_release' => $hit['personal_release'],
                            'uploader'         => $hit['anon'] ? 'Anonymous' : $hit['user']['username'],
                            'seeders'          => $hit['seeders'],
                            'leechers'         => $hit['leechers'],
                            'times_completed'  => $hit['times_completed'],
                            'tmdb_id'          => $hit['tmdb_movie_id'] ?: $hit['tmdb_tv_id'] ?: 0,
                            'imdb_id'          => $hit['imdb'],
                            'tvdb_id'          => $hit['tvdb'],
                            'mal_id'           => $hit['mal'],
                            'igdb_id'          => $hit['igdb'],
                            'category_id'      => $hit['category']['id'] ?? null,
                            'type_id'          => $hit['type']['id'] ?? null,
                            'resolution_id'    => $hit['resolution']['id'] ?? null,
                            'created_at'       => date('Y-m-d\TH:i:s', $hit['created_at']).'.000000Z',
                            'details_link'     => route('torrents.show', ['id' => $hit['id']]),
                        ]
                    ]);
                }

                /** @phpstan-ignore method.notFound (this method exists at time of writing) */
                $torrents = $paginator->setCollection(collect($torrents));
            }

            return [$torrents, $hasMore];
        });

        if ($isSqlAllowed) {
            return new TorrentsResource($torrents);
        }

        $page = $request->integer('page') ?: 1;
        $perPage = min(100, $request->integer('perPage') ?: 25);

        // Auth keys must not be cached
        $torrents->through(function ($torrent) {
            $torrent['attributes']['download_link'] = route('torrent.download.rsskey', ['id' => $torrent['id'], 'rsskey' => auth(AuthGuard::API->value)->user()->rsskey]);
            $torrent['attributes']['magnet_link'] = config('torrent.magnet') ? 'magnet:?dn='.$torrent['attributes']['name'].'&xt=urn:btih:'.$torrent['attributes']['info_hash'].'&as='.route('torrent.download.rsskey', ['id' => $torrent['id'], 'rsskey' => auth(AuthGuard::API->value)->user()->rsskey]).'&tr='.route('announce', ['passkey' => auth('api')->user()->passkey]).'&xl='.$torrent['attributes']['size'] : null;

            return $torrent;
        });

        return response()->json([
            'data'  => $torrents->items(),
            'links' => [
                'first' => $request->fullUrlWithoutQuery(['page' => 1]),
                'last'  => null,
                'prev'  => $page === 1 ? null : $request->fullUrlWithQuery(['page' => $page - 1]),
                'next'  => $hasMore ? $request->fullUrlWithQuery(['page' => $page + 1]) : null,
                'self'  => $request->fullUrl(),
            ],
            'meta' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'from'         => ($page - 1) * $perPage + 1,
                'to'           => ($page - 1) * $perPage + \count($torrents->items()),
            ]
        ]);
    }
}
