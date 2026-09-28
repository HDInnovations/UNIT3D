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

use App\Http\Resources\SubtitleResource;
use App\Models\Apikey;
use App\Models\MediaLanguage;
use App\Models\Subtitle;
use App\Models\TmdbMovie;
use App\Models\Torrent;
use App\Services\SubtitleDownloadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Closure;

/**
 * Read-only subtitle API used by external subtitle managers (e.g. Bazarr).
 *
 * Only approved subtitles attached to approved torrents are ever exposed: the
 * ApprovedScope global scopes on both models are intentionally left in place.
 */
class SubtitleController extends BaseController
{
    /**
     * Version of this API's request/response contract.
     */
    public const int API_VERSION = 1;

    /**
     * Default number of subtitles per page.
     */
    private const int PER_PAGE = 25;

    /**
     * Maximum number of subtitles per page.
     */
    private const int MAX_PER_PAGE = 50;

    public function __construct(private readonly SubtitleDownloadService $subtitleDownloadService)
    {
    }

    /**
     * Report that the subtitle API is available, the API key is valid, and
     * whether the API key is allowed to search and download subtitles.
     */
    public function status(Request $request): \Illuminate\Http\JsonResponse
    {
        $apikey = Apikey::query()->where('content', '=', $request->bearerToken())->sole();

        return response()->json([
            'status'      => 'ok',
            'provider'    => 'unit3d',
            'version'     => config('unit3d.version'),
            'api_version' => self::API_VERSION,
            'permissions' => [
                'search'   => (bool) $apikey->can_search,
                'download' => (bool) $apikey->can_download,
            ],
        ]);
    }

    /**
     * Search subtitles for a movie.
     *
     * Movies are matched by TMDB id first, then by IMDb id (only when the TMDB
     * id is absent or matches nothing). Exact title and release year are used
     * only when neither id is given.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $validated = $request->validate([
            'tmdb_id' => [
                'required_without_all:imdb_id,title',
                'nullable',
                'integer',
                'min:1',
                'max:4294967295',
            ],
            'imdb_id' => [
                'required_without_all:tmdb_id,title',
                'nullable',
                'string',
                'regex:/^(tt)?0*[1-9]\d{0,9}$/',
            ],
            'title' => [
                'required_without_all:tmdb_id,imdb_id',
                'nullable',
                'string',
                'max:255',
            ],
            'year' => [
                'required_with:title',
                'nullable',
                'integer',
                'between:1870,2100',
            ],
            'language' => [
                'nullable',
                'string',
                'regex:/^[a-z]{2}(,[a-z]{2}){0,49}$/i',
            ],
            'perPage' => [
                'nullable',
                'integer',
                'between:1,'.self::MAX_PER_PAGE,
            ],
        ]);

        $languages = isset($validated['language'])
            ? array_values(array_unique(explode(',', strtolower((string) $validated['language']))))
            : [];
        $perPage = (int) ($validated['perPage'] ?? self::PER_PAGE);

        $matchedBy = match (true) {
            isset($validated['tmdb_id']) => 'tmdb',
            isset($validated['imdb_id']) => 'imdb',
            default                      => 'title',
        };
        $subtitles = $this->search($this->movieConstraint($matchedBy, $validated), $languages, $perPage);

        // IMDb is only used when the TMDB id matches nothing
        if ($matchedBy === 'tmdb' && isset($validated['imdb_id']) && $subtitles->total() === 0) {
            $matchedBy = 'imdb';
            $subtitles = $this->search($this->movieConstraint($matchedBy, $validated), $languages, $perPage);
        }

        return SubtitleResource::collection($subtitles->withQueryString())
            ->additional(['meta' => ['matched_by' => $matchedBy]]);
    }

    /**
     * Download an approved subtitle.
     */
    public function download(Request $request, int $id): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $subtitle = Subtitle::query()->with(['language', 'torrent'])->findOrFail($id);

        if (!$this->subtitleDownloadService->userCanDownload($request->user(), $subtitle)) {
            return $this->sendError('Your download rights have been revoked!', code: 403);
        }

        return $this->subtitleDownloadService->download($subtitle);
    }

    /**
     * Get the torrent constraint matching the requested movie.
     *
     * Title and year are only used when no id is known, and must match exactly.
     *
     * @param  'tmdb'|'imdb'|'title'            $matchedBy
     * @param  array<string, mixed>             $validated
     * @return Closure(Builder<Torrent>): mixed
     */
    private function movieConstraint(string $matchedBy, array $validated): Closure
    {
        return match ($matchedBy) {
            'tmdb'  => fn (Builder $query) => $query->where('tmdb_movie_id', '=', (int) $validated['tmdb_id']),
            'imdb'  => fn (Builder $query) => $query->where('imdb', '=', (int) ltrim((string) $validated['imdb_id'], 't')),
            'title' => fn (Builder $query) => $query->whereIn(
                'tmdb_movie_id',
                TmdbMovie::query()
                    ->select('id')
                    ->where('title', '=', $validated['title'])
                    ->whereYear('release_date', '=', (int) $validated['year'])
            ),
        };
    }

    /**
     * Paginate approved subtitles of the approved torrents matching the constraint.
     *
     * @param  Closure(Builder<Torrent>): mixed                           $torrentConstraint
     * @param  list<string>                                               $languages
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Subtitle>
     */
    private function search(Closure $torrentConstraint, array $languages, int $perPage): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Subtitle::query()
            ->with([
                'language:id,name,code',
                'torrent:id,name,tmdb_movie_id,imdb',
            ])
            ->whereIn('torrent_id', Torrent::query()->select('id')->where($torrentConstraint))
            ->when($languages !== [], fn (Builder $query) => $query->whereIn(
                'language_id',
                MediaLanguage::query()->select('id')->whereIn('code', $languages)
            ))
            ->orderByDesc('downloads')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
