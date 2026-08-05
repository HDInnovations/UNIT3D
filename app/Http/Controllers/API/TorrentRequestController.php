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

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTorrentRequestRequest;
use App\Http\Resources\TorrentRequestResource;
use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Repositories\ChatRepository;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorrentRequestController extends Controller
{
    /**
     * TorrentRequestController Constructor.
     */
    public function __construct(
        private readonly ChatRepository $chatRepository
    ) {
    }

    /**
     * Request search filter.
     */
    public function filter(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = TorrentRequest::query()
            ->with(['user', 'claim.user', 'filler'])
            ->withSum('bounties', 'seedbonus')
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'LIKE', '%'.str_replace(' ', '%', $request->input('name')).'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->whereIntegerInRaw('category_id', (array) $request->input('category_id')))
            ->when($request->filled('type_id'), fn ($query) => $query->whereIntegerInRaw('type_id', (array) $request->input('type_id')))
            ->when($request->filled('resolution_id'), fn ($query) => $query->whereIntegerInRaw('resolution_id', (array) $request->input('resolution_id')))
            ->when($request->filled('tmdb'), fn ($query) => $query->whereAny(['tmdb_movie_id', 'tmdb_tv_id'], '=', $request->integer('tmdb')))
            ->when($request->filled('imdb'), fn ($query) => $query->where('imdb', '=', $request->integer('imdb')))
            ->when($request->filled('tvdb'), fn ($query) => $query->where('tvdb', '=', $request->integer('tvdb')))
            ->when($request->filled('mal'), fn ($query) => $query->where('mal', '=', $request->integer('mal')))
            ->when($request->filled('filled'), fn ($query) => $request->boolean('filled')
                ? $query->whereNotNull('filled_by')
                : $query->whereNull('filled_by'))
            ->when($request->filled('claimed'), fn ($query) => $request->boolean('claimed')
                ? $query->whereNotNull('claim')
                : $query->whereNull('claim'));

        $perPage = min($request->integer('perPage', 25), 100);
        $page = max($request->integer('page', 1), 1);
        $requests = $query->paginate(
            perPage: $perPage,
            page: $page
        );

        return TorrentRequestResource::collection($requests)->response();
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(StoreTorrentRequestRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $torrentRequest = DB::transaction(function () use ($request, $user): TorrentRequest {
            $user->decrement('seedbonus', $request->bounty);

            $torrentRequest = TorrentRequest::create(['user_id' => $user->id] + $request->safe()->except(['bounty', 'anon']));

            TorrentRequestBounty::create([
                'user_id'     => $user->id,
                'seedbonus'   => $request->bounty,
                'requests_id' => $torrentRequest->id,
                'anon'        => $request->anon,
            ]);

            return $torrentRequest;
        });

        // Auto Shout
        if (!$torrentRequest->anon) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has created a new request [url=%s]%s[/url]', href_profile($user), $user->username, href_request($torrentRequest), $torrentRequest->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has created a new request [url=%s]%s[/url]', href_request($torrentRequest), $torrentRequest->name)
            );
        }

        match (true) {
            $torrentRequest->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrentRequest->tmdb_tv_id),
            $torrentRequest->tmdb_movie_id !== null => new TMDBScraper()->movie($torrentRequest->tmdb_movie_id),
            $torrentRequest->igdb !== null          => new IgdbScraper()->game($torrentRequest->igdb),
            default                                 => null,
        };

        $torrentRequest->load(['user', 'claim.user', 'filler'])->loadSum('bounties', 'seedbonus');

        return (new TorrentRequestResource($torrentRequest))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * View a single request.
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $request = TorrentRequest::with(['user', 'claim.user', 'filler'])
            ->withSum('bounties', 'seedbonus')
            ->findOrFail($id);

        return new TorrentRequestResource($request)->response();
    }
}
