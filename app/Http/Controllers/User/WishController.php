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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishRequest;
use App\Models\Category;
use App\Models\User;
use App\Models\Wish;
use App\Services\Tmdb\Client\Movie;
use App\Services\Tmdb\Client\TV;
use Illuminate\Http\Request;
use JsonException;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\WishControllerTest
 */
class WishController extends Controller
{
    /**
     * Get A Users Wishlist.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        return view('user.wish.index', [
            'user'   => $user,
            'wishes' => $user->wishes()
                ->withCount(['movieTorrents', 'tvTorrents'])
                ->latest()
                ->paginate(25),
            'movieCategoryIds' => Category::where('movie_meta', '=', 1)->pluck('id')->toArray(),
            'tvCategoryIds'    => Category::where('tv_meta', '=', 1)->pluck('id')->toArray(),
            'route'            => 'wish',
        ]);
    }

    /**
     * Add New Wish.
     *
     * @throws JsonException
     */
    public function store(StoreWishRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        switch ($request->meta) {
            case 'movie':
                $meta = (new Movie((int) $request->tmdb_movie_id))->data;

                if ($meta === null) {
                    return to_route('users.wishes.index', ['user' => $user])
                        ->withErrors('TMDB Bad Request!');
                }

                $title = $meta['title'].' ('.$meta['release_date'].')';

                Wish::create([
                    'user_id'       => $user->id,
                    'title'         => $title,
                    'tmdb_movie_id' => $request->tmdb_movie_id,
                ]);

                break;
            case 'tv':
                $meta = (new TV((int) $request->tmdb_tv_id))->data;

                if ($meta === null) {
                    return to_route('users.wishes.index', ['user' => $user])
                        ->withErrors('TMDB Bad Request!');
                }

                $title = $meta['name'].' ('.$meta['first_air_date'].')';

                Wish::create([
                    'user_id'    => $user->id,
                    'title'      => $title,
                    'tmdb_tv_id' => $request->tmdb_tv_id,
                ]);

                break;
        }

        return to_route('users.wishes.index', ['user' => $user])
            ->with('success', 'Wish successfully added!');
    }

    /**
     * Delete A Wish.
     */
    public function destroy(Request $request, User $user, Wish $wish): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        $wish->delete();

        return to_route('users.wishes.index', ['user' => $user])
            ->with('success', 'Wish successfully removed!');
    }
}
