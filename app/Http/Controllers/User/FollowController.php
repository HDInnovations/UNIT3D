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
use App\Models\User;
use App\Notifications\NewFollow;
use App\Notifications\NewUnfollow;
use Illuminate\Http\Request;

/**
 * @see \Tests\Feature\Http\Controllers\FollowControllerTest
 */
class FollowController extends Controller
{
    /**
     * User Followers.
     */
    public function index(User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('user.follower.index', [
            'followers' => $user->followers()->with('group')->orderByPivot('created_at', 'desc')->paginate(25),
            'user'      => $user,
        ]);
    }

    /**
     * Follow A User.
     */
    public function store(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        if ($request->user()->is($user)) {
            return to_route('users.show', ['user' => $user])
                ->withErrors(trans('user.follow-yourself'));
        }

        // verify if user is following the target user already
        if ($user->followers()->where('user_id', $request->user()->id)->exists()) {
            return to_route('users.show', ['user' => $user])
                ->withErrors(\sprintf('You are already following %s', $user->username));
        }

        $user->followers()->attach($request->user()->id);

        $user->notify(new NewFollow('user', $request->user()));

        return to_route('users.show', ['user' => $user])
            ->with('success', \sprintf(trans('user.follow-user'), $user->username));
    }

    /**
     * Un Follow A User.
     */
    public function destroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        // verify if user is following the target user already
        if (!$user->followers()->where('user_id', $request->user()->id)->exists()) {
            return to_route('users.show', ['user' => $user])
                ->withErrors(\sprintf('You\'re not following %s', $user->username));
        }

        $user->followers()->detach($request->user()->id);

        $user->notify(new NewUnfollow('user', $request->user()));

        return back()->with('success', \sprintf(trans('user.follow-revoked'), $user->username));
    }
}
