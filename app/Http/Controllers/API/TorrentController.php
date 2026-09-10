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
use App\Http\Requests\API\StoreTorrentRequest;
use App\Http\Resources\TorrentResource;
use App\Http\Resources\TorrentsResource;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            $torrents = Torrent::query()
                ->with([
                    'user:id,username',
                    'category',
                    'type',
                    'resolution',
                    'region',
                    'distributor',
                    'files'
                ])
                ->withExists([
                    'featured as featured'
                ])
                ->select('*')
                ->selectRaw(self::META_TYPE_CASE.' as meta')
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
    public function store(StoreTorrentRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user()->loadExists('internals');
        abort_unless($user->can_upload ?? $user->group->can_upload, 403, __('torrent.cant-upload').' '.__('torrent.cant-upload-desc'));

        abort_if(\is_array($request->file('torrent')), 400);

        abort_if(\is_array($request->file('nfo')), 400);

        // Move and decode the torrent temporarily
        $decodedTorrent = TorrentTools::normalizeTorrent($request->file('torrent'));

        $meta = Bencode::get_meta($decodedTorrent);

        $fileName = uniqid('', true).'.torrent'; // Generate a unique name
        Storage::disk('torrent-files')->put($fileName, Bencode::bencode($decodedTorrent));

        // Create the torrent (DB)
        $torrent = Torrent::query()->create([
            ...$request->safe()->except(['torrent', 'featured']),
            'mediainfo'    => TorrentTools::anonymizeMediainfo($request->filled('mediainfo') ? $request->string('mediainfo') : null),
            'info_hash'    => Bencode::get_infohash($decodedTorrent),
            'file_name'    => $fileName,
            'num_file'     => $meta['count'],
            'folder'       => Bencode::get_name($decodedTorrent),
            'size'         => $meta['size'],
            'nfo'          => $request->hasFile('nfo') ? TorrentTools::getNfo($request->file('nfo')) : '',
            'user_id'      => $user->id,
            'moderated_at' => now(),
            'moderated_by' => User::SYSTEM_USER_ID,
        ]);

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
            TorrentFile::query()->insert($files->toArray());
        }

        // Set torrent to featured
        if ($request->validated('featured')) {
            FeaturedTorrent::query()->create([
                'user_id'    => $user->id,
                'torrent_id' => $torrent->id,
            ]);
        }

        // Tracker updates come after database updates in case tracker's offline

        Unit3dAnnounce::addTorrent($torrent);

        if ($request->validated('featured')) {
            Unit3dAnnounce::addFeaturedTorrent($torrent->id);
        }

        // Metadata updates come after tracker updates in case TMDB or IGDB is offline

        match (true) {
            $torrent->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrent->tmdb_tv_id),
            $torrent->tmdb_movie_id !== null => new TMDBScraper()->movie($torrent->tmdb_movie_id),
            $torrent->igdb !== null          => new IgdbScraper()->game($torrent->igdb),
            default                          => null,
        };

        // Torrent Keywords System
        $keywords = [];

        foreach (TorrentTools::parseKeywords($request->string('keywords')) as $keyword) {
            $keywords[] = ['torrent_id' => $torrent->id, 'name' => $keyword];
        }

        foreach (collect($keywords)->chunk(intdiv(65_000, 2)) as $keywords) {
            Keyword::query()->upsert($keywords->toArray(), ['torrent_id', 'name']);
        }

        // check for trusted user & mod queue isn't opted in and update torrent
        if ($user->group->is_trusted && !$request->boolean('mod_queue_opt_in')) {
            $user = $torrent->user;
            $username = $user->username;
            $anon = $torrent->anon;
            $featured = $request->validated('featured');
            $free = $torrent->free;
            $doubleup = $torrent->doubleup;

            // Announce To Shoutbox
            if (!$anon) {
                $this->chatRepository->systemMessage(
                    'User [url='.href_profile($user).']'.$username.'[/url] has uploaded a new '.$torrent->category->name.'. [url='.href_torrent($torrent).']'.$torrent->name.'[/url], grab it now!'
                );
            } else {
                $this->chatRepository->systemMessage(
                    'An anonymous user has uploaded a new '.$torrent->category->name.'. [url='.href_torrent($torrent).']'.$torrent->name.'[/url], grab it now!'
                );
            }

            if ($anon && $featured == 1) {
                $this->chatRepository->systemMessage(
                    'Ladies and Gents, [url='.href_torrent($torrent).$torrent->id.']'.$torrent->name.'[/url] has been added to the Featured Torrents Slider by an anonymous user! Grab It While You Can!'
                );
            } elseif (!$anon && $featured == 1) {
                $this->chatRepository->systemMessage(
                    'Ladies and Gents, [url='.href_torrent($torrent).']'.$torrent->name.'[/url] has been added to the Featured Torrents Slider by [url='.href_profile($user).']'.$username.'[/url]! Grab It While You Can!'
                );
            }

            if ($free >= 1 && $featured == 0) {
                if ($torrent->fl_until === null) {
                    $this->chatRepository->systemMessage(
                        'Ladies and Gents, [url='.href_torrent($torrent).']'.$torrent->name.'[/url] has been granted '.$free.'% FreeLeech! Grab It While You Can!'
                    );
                } else {
                    $this->chatRepository->systemMessage(
                        'Ladies and Gents, [url='.href_torrent($torrent).']'.$torrent->name.'[/url] has been granted '.$free.'% FreeLeech for '.$request->input('fl_until').' days.'
                    );
                }
            }

            if ($doubleup == 1 && $featured == 0) {
                if ($torrent->du_until === null) {
                    $this->chatRepository->systemMessage(
                        'Ladies and Gents, [url='.href_torrent($torrent).']'.$torrent->name.'[/url] has been granted Double Upload! Grab It While You Can!'
                    );
                } else {
                    $this->chatRepository->systemMessage(
                        'Ladies and Gents, [url='.href_torrent($torrent).']'.$torrent->name.'[/url] has been granted Double Upload for '.$request->input('du_until').' days.'
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
        $torrent = Torrent::query()
            ->with(['user:id,username', 'category', 'type', 'resolution', 'region', 'distributor', 'files'])
            ->select('*')
            ->selectRaw(self::META_TYPE_CASE.' as meta')
            ->findOrFail($id);

        $torrent->setAttribute('meta', null);

        if ($torrent->category->tv_meta && $torrent->tmdb_tv_id) {
            $torrent->setAttribute('meta', TmdbTv::query()->with(['genres'])->find($torrent->tmdb_tv_id));
        }

        if ($torrent->category->movie_meta && $torrent->tmdb_movie_id) {
            $torrent->setAttribute('meta', TmdbMovie::query()->with(['genres'])->find($torrent->tmdb_movie_id));
        }

        if ($torrent->category->game_meta && $torrent->igdb) {
            $torrent->setAttribute('meta', IgdbGame::query()->with(['genres'])->find($torrent->igdb));
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
                ->selectRaw(self::META_TYPE_CASE.' as meta');

            $filters = new TorrentSearchFiltersDTO(
                name: $request->filled('name') ? $request->string('name')->toString() : '',
                description: $request->filled('description') ? $request->string('description')->toString() : '',
                mediainfo: $request->filled('mediainfo') ? $request->string('mediainfo')->toString() : '',
                uploader: $request->filled('uploader') ? $request->string('uploader')->toString() : '',
                keywords: $request->filled('keywords') ? array_map(trim(...), explode(',', $request->string('keywords')->toString())) : [],
                startYear: $request->filled('startYear') ? $request->integer('startYear') : null,
                endYear: $request->filled('endYear') ? $request->integer('endYear') : null,
                categoryIds: $request->filled('categories') ? array_map(intval(...), $request->categories) : [],
                typeIds: $request->filled('types') ? array_map(intval(...), $request->types) : [],
                resolutionIds: $request->filled('resolutions') ? array_map(intval(...), $request->resolutions) : [],
                genreIds: $request->filled('genres') ? array_map(intval(...), $request->genres) : [],
                tmdbId: $request->filled('tmdbId') ? $request->integer('tmdbId') : null,
                imdbId: $request->filled('imdbId') ? $request->integer('imdbId') : null,
                tvdbId: $request->filled('tvdbId') ? $request->integer('tvdbId') : null,
                malId: $request->filled('malId') ? $request->integer('malId') : null,
                playlistId: $request->filled('playlistId') ? $request->integer('playlistId') : null,
                collectionId: $request->filled('collectionId') ? $request->integer('collectionId') : null,
                primaryLanguageNames: $request->filled('primaryLanguages') ? array_map(str(...), $request->primaryLanguages) : [],
                adult: $request->filled('adult') ? $request->boolean('adult') : null,
                free: $request->filled('free') ? array_map(intval(...), (array) $request->free) : [],
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
                            'info_hash'        => $hit['info_hash'],
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

            // Info hash is only used for the magnet link if it's enabled. Make sure it's erased from the response before returned to the user.
            unset($torrent['attributes']['info_hash']);

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
