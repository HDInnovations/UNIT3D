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

use App\Http\Requests\StoreTorrentRequestRequest;
use App\Http\Requests\UpdateTorrentRequestRequest;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\Type;
use App\Repositories\ChatRepository;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RequestControllerTest
 */
class RequestController extends Controller
{
    /**
     * RequestController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('requests.index');
    }

    /**
     * Display The Torrent Request.
     */
    public function show(Request $request, TorrentRequest $torrentRequest): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('requests.show', [
            'torrentRequest' => $torrentRequest->load([
                'bounties.user.group',
                'category',
                'claim.user.group',
                'game' => [
                    'genres',
                    'companies',
                    'platforms',
                ],
                'movie' => [
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'collections.movies'
                ],
                'resolution',
                'torrent',
                'tv' => [
                    'genres',
                    'credits' => ['person', 'occupation'],
                    'companies',
                    'networks',
                ],
                'type',
                'user.group',
            ]),
            'user'    => $request->user()->load('group'),
            'canEdit' => $request->user()->group->is_modo || TorrentRequest::query()
                ->whereDoesntHave('bounties', fn ($query) => $query->where('user_id', '!=', $request->user()->id))
                ->whereDoesntHave('claim')
                ->whereNull('filled_by')
                ->whereKey($torrentRequest)
                ->exists(),
        ]);
    }

    /**
     * Torrent Request Add Form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('requests.create', [
            'categories' => Category::orderBy('position')
                ->get()
                ->mapWithKeys(fn ($category) => [$category->id => [
                    'name' => $category->name,
                    'type' => match (true) {
                        $category->movie_meta => 'movie',
                        $category->tv_meta    => 'tv',
                        $category->game_meta  => 'game',
                        $category->music_meta => 'music',
                        $category->no_meta    => 'no',
                        default               => 'no',
                    },
                ]])
                ->toArray(),
            'types'       => Type::orderBy('position')->get(),
            'resolutions' => Resolution::orderBy('position')->get(),
            'user'        => $request->user(),
            'category_id' => $request->category_id ?? Category::first('id')->id,
            'title'       => urldecode((string) $request->title),
            'imdb'        => $request->imdb,
            'movieId'     => $request->tmdb_movie_id,
            'tvId'        => $request->tmdb_tv_id,
            'mal'         => $request->mal,
            'tvdb'        => $request->tvdb,
            'igdb'        => $request->igdb,
        ]);
    }

    /**
     * Store A New Torrent Request.
     */
    public function store(StoreTorrentRequestRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $user->decrement('seedbonus', $request->bounty);

        $torrentRequest = TorrentRequest::create(['user_id' => $request->user()->id] + $request->validated());

        TorrentRequestBounty::create([
            'user_id'     => $user->id,
            'seedbonus'   => $request->bounty,
            'requests_id' => $torrentRequest->id,
            'anon'        => $request->anon,
        ]);

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

        return to_route('requests.index')
            ->with('success', trans('request.added-request'));
    }

    /**
     * Torrent Request Edit Form.
     */
    public function edit(Request $request, TorrentRequest $torrentRequest): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('requests.edit', [
            'categories' => Category::query()
                ->orderBy('position')
                ->get()
                ->mapWithKeys(fn ($cat) => [
                    $cat['id'] => [
                        'name' => $cat['name'],
                        'type' => match (true) {
                            $cat->movie_meta => 'movie',
                            $cat->tv_meta    => 'tv',
                            $cat->game_meta  => 'game',
                            $cat->music_meta => 'music',
                            $cat->no_meta    => 'no',
                            default          => 'no',
                        },
                    ]
                ]),
            'types'          => Type::orderBy('position')->get(),
            'resolutions'    => Resolution::orderBy('position')->get(),
            'user'           => $request->user(),
            'torrentRequest' => $torrentRequest,
        ]);
    }

    /**
     * Edit A Torrent Request.
     */
    public function update(UpdateTorrentRequestRequest $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            (
                $user->group->is_modo
                || (
                    $user->id === $torrentRequest->user_id
                    && TorrentRequest::query()
                        ->whereDoesntHave('bounties', fn ($query) => $query->where('user_id', '!=', $request->user()->id))
                        ->whereDoesntHave('claim')
                        ->whereNull('filled_by')
                        ->whereKey($torrentRequest)
                        ->exists()
                )
            ),
            403
        );

        $torrentRequest->update($request->validated());

        match (true) {
            $torrentRequest->tmdb_tv_id !== null    => new TMDBScraper()->tv($torrentRequest->tmdb_tv_id),
            $torrentRequest->tmdb_movie_id !== null => new TMDBScraper()->movie($torrentRequest->tmdb_movie_id),
            $torrentRequest->igdb !== null          => new IgdbScraper()->game($torrentRequest->igdb),
            default                                 => null,
        };

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.edited-request'));
    }

    /**
     * Delete A Torrent Request.
     *
     * @throws Exception
     */
    public function destroy(Request $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user->group->is_modo
            || (
                $torrentRequest->user_id === $user->id
                && TorrentRequest::query()
                    ->whereDoesntHave('bounties', fn ($query) => $query->where('user_id', '!=', $request->user()->id))
                    ->whereDoesntHave('claim')
                    ->whereNull('filled_by')
                    ->whereKey($torrentRequest)
                    ->exists()
            ),
            403
        );

        $torrentRequest->bounties()->delete();
        $torrentRequest->delete();

        return to_route('requests.index')
            ->with('success', \sprintf(trans('request.deleted'), $torrentRequest->name));
    }
}
