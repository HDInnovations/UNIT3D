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

namespace App\Http\Controllers;

use App\DTO\TorrentSearchFiltersDTO;
use App\Models\Category;
use App\Models\TmdbGenre;
use App\Models\Group;
use App\Models\Resolution;
use App\Models\Rss;
use App\Models\Torrent;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;
use Meilisearch\Endpoints\Indexes;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RssControllerTest
 */
class RssController extends Controller
{
    /**
     * Display a listing of the RSS resource.
     */
    public function index(Request $request, ?string $hash = null): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('rss.index', [
            'hash'        => $hash,
            'public_rss'  => Rss::where('is_private', '=', false)->oldest('position')->get(),
            'private_rss' => Rss::where('is_private', '=', true)->where('user_id', '=', $request->user()->id)->latest()->get(),
            'user'        => $request->user(),
        ]);
    }

    /**
     * Show the form for creating a new RSS resource.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('rss.create', [
            'categories'  => Category::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'types'       => Type::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'resolutions' => Resolution::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'genres'      => TmdbGenre::orderBy('name')->get(),
            'user'        => $request->user(),
        ]);
    }

    /**
     * Store a newly created RSS resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        $v = validator($request->all(), [
            'name'          => 'required|min:3|max:255',
            'search'        => 'max:255',
            'description'   => 'max:255',
            'uploader'      => 'max:255',
            'categories'    => 'sometimes|array|max:999',
            'categories.*'  => 'sometimes|exists:categories,id',
            'types'         => 'sometimes|array|max:999',
            'types.*'       => 'sometimes|exists:types,id',
            'resolutions'   => 'sometimes|array|max:999',
            'resolutions.*' => 'sometimes|exists:resolutions,id',
            'genres'        => 'sometimes|array|max:999',
            'genres.*'      => 'sometimes|exists:tmdb_genres,id',
            'position'      => 'sometimes|integer|max:9999',
        ]);

        $params = $request->only([
            'name',
            'search',
            'description',
            'uploader',
            'imdb',
            'tvdb',
            'tmdb',
            'mal',
            'categories',
            'types',
            'resolutions',
            'genres',
            'freeleech',
            'doubleupload',
            'featured',
            'highspeed',
            'internal',
            'personalrelease',
            'bookmark',
            'alive',
            'dying',
            'dead',
        ]);

        if ($v->passes()) {
            $rss = new Rss();
            $rss->name = $request->input('name');
            $rss->user_id = $request->user()->id;
            $expected = $rss->expected_fields;
            $rss->json_torrent = array_merge($expected, $params);
            $rss->is_private = true;
            $rss->save();

            return to_route('rss.index', ['hash' => 'private'])
                ->with('success', trans('rss.created'));
        }

        return to_route('rss.create')
            ->withErrors($v->errors());
    }

    /**
     * Display the specified RSS resource.
     *
     * @throws Exception
     */
    public function show(Request $request, int $id): \Illuminate\Http\Response
    {
        $user = $request->user();

        // Redis returns ints as numeric strings!
        $disabledGroupId = (int) cache()->rememberForever('group:disabled:id', fn () => Group::where('slug', '=', 'disabled')->soleValue('id'));

        abort_if($user->group_id === $disabledGroupId, 404);

        $rss = Rss::query()
            ->where(
                fn ($query) => $query
                    ->where('user_id', '=', $user->id)
                    ->orWhere('is_private', '=', false)
            )
            ->findOrFail($id);

        $search = $rss->object_torrent;

        if (\is_object($search)) {
            $cacheKey = 'rss:'.$rss->id;

            $torrents = cache()->flexible($cacheKey, [60 * 5, 60 * 6], function () use ($search) {
                $filters = new TorrentSearchFiltersDTO(
                    name: $search->search ?? '',
                    description: $search->description ?? '',
                    uploader: $search->uploader ?? '',
                    categoryIds: array_map('intval', $search->categories ?? []),
                    typeIds: array_map('intval', $search->types ?? []),
                    resolutionIds: array_map('intval', $search->resolutions ?? []),
                    genreIds: array_map('intval', $search->genres ?? []),
                    tmdbId: $search->tmdb === null ? null : (int) $search->tmdb,
                    imdbId: $search->imdb === null ? null : ((int) (preg_match('/tt0*(?=(\d{7,}))/', $search->imdb, $matches) ? $matches[1] : $search->imdb)),
                    tvdbId: $search->tvdb === null ? null : (int) $search->tvdb,
                    malId: $search->mal === null ? null : (int) $search->mal,
                    free: $search->freeleech === null ? [] : [25, 50, 75, 100],
                    doubleup: (bool) ($search->doubleupload ?? false),
                    featured: (bool) ($search->featured ?? false),
                    highspeed: (bool) ($search->highspeed ?? false),
                    userBookmarked: (bool) ($search->bookmark ?? false),
                    internal: (bool) ($search->internal ?? false),
                    personalRelease: (bool) ($search->personalrelease ?? false),
                    alive: (bool) ($search->alive ?? false),
                    dying: (bool) ($search->dying ?? false),
                    dead: (bool) ($search->dead ?? false),
                );

                $results = Torrent::search(
                    $search->search ?? '',
                    function (Indexes $meilisearch, string $query, array $options) use ($filters) {
                        $options['limit'] = 50;
                        $options['sort'] = [
                            'bumped_at:desc',
                        ];
                        $options['filter'] = $filters->toMeilisearchFilter();
                        $options['matchingStrategy'] = 'all';

                        $results = $meilisearch->search($query, $options);

                        return $results;
                    }
                )
                    ->raw();

                return $results['hits'] ?? [];
            });

            return response()->view('rss.show', [
                'torrents' => $torrents,
                'user'     => $user,
                'rss'      => $rss,
            ])
                ->header('Content-Type', 'text/xml');
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified RSS resource.
     */
    public function edit(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();
        $rss = Rss::where('is_private', '=', true)->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $rss->user_id, 403);

        return view('rss.edit', [
            'categories'  => Category::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'types'       => Type::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'resolutions' => Resolution::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'genres'      => TmdbGenre::orderBy('name')->get(),
            'user'        => $user,
            'rss'         => $rss,
        ]);
    }

    /**
     * Update the specified RSS resource in storage.
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        $user = $request->user();
        $rss = Rss::where('is_private', '=', true)->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $rss->user_id, 403);

        $v = validator($request->all(), [
            'search'        => 'max:255',
            'description'   => 'max:255',
            'uploader'      => 'max:255',
            'categories'    => 'sometimes|array|max:999',
            'categories.*'  => 'sometimes|exists:categories,id',
            'types'         => 'sometimes|array|max:999',
            'types.*'       => 'sometimes|exists:types,id',
            'resolutions'   => 'sometimes|array|max:999',
            'resolutions.*' => 'sometimes|exists:resolutions,id',
            'genres'        => 'sometimes|array|max:999',
            'genres.*'      => 'sometimes|exists:tmdb_genres,id',
            'position'      => 'sometimes|integer|max:9999',
        ]);

        $params = $request->only([
            'search',
            'description',
            'uploader',
            'imdb',
            'tvdb',
            'tmdb',
            'mal',
            'categories',
            'types',
            'resolutions',
            'genres',
            'freeleech',
            'doubleupload',
            'featured',
            'highspeed',
            'internal',
            'personalrelease',
            'bookmark',
            'alive',
            'dying',
            'dead',
        ]);

        if ($v->passes()) {
            $expected = $rss->expected_fields;
            $push = array_merge($expected, $params);
            $rss->json_torrent = array_merge($rss->json_torrent, $push);
            $rss->is_private = true;
            $rss->save();

            return to_route('rss.index', ['hash' => 'private'])
                ->with('success', trans('rss.created'));
        }

        return to_route('rss.create', ['id' => $id])
            ->withErrors($v->errors());
    }

    /**
     * Remove the specified RSS resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        $user = $request->user();
        $rss = Rss::where('is_private', '=', true)->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $rss->user_id, 403);

        $rss->delete();

        return to_route('rss.index', ['hash' => 'private'])
            ->with('success', trans('rss.deleted'));
    }
}
