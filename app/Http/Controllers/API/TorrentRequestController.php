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
use App\Http\Resources\TorrentRequestResource;
use App\Models\TorrentRequest;
use Illuminate\Http\Request;

class TorrentRequestController extends Controller
{
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
