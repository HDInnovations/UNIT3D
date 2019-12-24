<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     Poppabear
 */

namespace App\Http\Controllers;

use App\Interfaces\WishInterface;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;

final class WishController extends Controller
{
    /**
     * @var WishInterface
     */
    private WishInterface $wish;
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $viewFactory;
    /**
     * @var \Illuminate\Routing\Redirector
     */
    private $redirector;

    /**
     * WishController Constructor.
     *
     * @param  WishInterface  $wish
     * @param  \Illuminate\Contracts\View\Factory  $viewFactory
     * @param  \Illuminate\Routing\Redirector  $redirector
     */
    public function __construct(WishInterface $wish, Factory $viewFactory, Redirector $redirector)
    {
        $this->wish = $wish;
        $this->viewFactory = $viewFactory;
        $this->redirector = $redirector;
    }

    /**
     * Get A Users Wishlist.
     *
     * @param  Request  $request
     * @param $username
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $username): Factory
    {
        $user = User::with('wishes')->where('username', '=', $username)->firstOrFail();

        abort_unless(($request->user()->group->is_modo || $request->user()->id == $user->id), 403);

        $wishes = $user->wishes()->latest()->paginate(25);

        return $this->viewFactory->make('user.wishlist', [
            'user'               => $user,
            'wishes'             => $wishes,
            'route'              => 'wish',
        ]);
    }

    /**
     * Add New Wish.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $imdb = Str::startsWith($request->get('imdb'), 'tt') ? $request->get('imdb') : 'tt'.$request->get('imdb');

        if ($this->wish->exists($user->id, $imdb)) {
            return $this->redirector
                ->route('wishes.index', ['id' => $uid])
                ->withErrors('Wish already exists!');
        }

        $omdb = $this->wish->omdbRequest($imdb);
        if ($omdb === null || $omdb === false) {
            return $this->redirector
                ->route('wishes.index', ['id' => $uid])
                ->withErrors('IMDB Bad Request!');
        }

        $source = $this->wish->getSource($imdb);

        $this->wish->create([
            'title'   => $omdb['Title'].' ('.$omdb['Year'].')',
            'type'    => $omdb['Type'],
            'imdb'    => $imdb,
            'source'  => $source,
            'user_id' => $user->id,
        ]);

        return $this->redirector
            ->route('wishes.index', ['username' => $user->username])
            ->withSuccess('Wish Successfully Added!');
    }

    /**
     * Delete A Wish.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param                            $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $user = $request->user();

        $this->wish->delete($id);

        return $this->redirector
            ->route('wishes.index', ['username' => $user->username])
            ->withSuccess('Wish Successfully Removed!');
    }
}
