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

namespace App\Http\Controllers\Staff;

use App\Enums\UserGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UpdateUserRequest;
use App\Models\Comment;
use App\Models\FailedLoginAttempt;
use App\Models\FreeleechToken;
use App\Models\Group;
use App\Models\History;
use App\Models\Internal;
use App\Models\Like;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Peer;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Scopes\ApprovedScope;
use App\Models\Thank;
use App\Models\Topic;
use App\Models\Torrent;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\UserControllerTest
 */
class UserController extends Controller
{
    /**
     * Users List.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.user.index');
    }

    /**
     * User Edit Form.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $group = $request->user()->group;

        return view('Staff.user.edit', [
            'user'      => $user,
            'groups'    => Group::when(!$group->is_owner, fn ($query) => $query->where('level', '<=', $group->level))->orderBy('position')->get(),
            'internals' => Internal::all(),
        ]);
    }

    /**
     * Edit A User.
     */
    public function update(UpdateUserRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $user->load('group');
        $staff = $request->user();
        $group = Group::findOrFail($request->group_id);

        abort_if(! $staff->group->is_owner && ($staff->group->level <= $user->group->level || $staff->group->level <= $group->level), 403);

        $user->update($request->validated());

        cache()->forget('user:'.$user->passkey);

        Unit3dAnnounce::addUser($user);

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Account was updated successfully!');
    }

    /**
     * Edit A Users Permissions.
     */
    public function permissions(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $user->update([
            'can_chat'    => $request->filled('can_chat') ? $request->boolean('can_chat') : null,
            'can_comment' => $request->filled('can_comment') ? $request->boolean('can_comment') : null,
            'can_invite'  => $request->filled('can_invite') ? $request->boolean('can_invite') : null,
            'can_request' => $request->filled('can_request') ? $request->boolean('can_request') : null,
            'can_upload'  => $request->filled('can_upload') ? $request->boolean('can_upload') : null,
            'is_donor'    => $request->boolean('is_donor'),
            'is_lifetime' => $request->boolean('is_lifetime'),
        ]);

        cache()->forget('user:'.$user->passkey);

        Unit3dAnnounce::addUser($user);

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Account permissions successfully edited');
    }

    /**
     * Delete A User.
     */
    protected function destroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_if($user->group->is_modo || $request->user()->is($user), 403);

        $user->update([
            'can_download' => false,
            'group_id'     => UserGroup::PRUNED->value,
            'deleted_by'   => auth()->id(),
        ]);

        Torrent::withoutGlobalScope(ApprovedScope::class)->where('user_id', '=', $user->id)->update([
            'user_id' => User::SYSTEM_USER_ID,
        ]);

        Comment::where('user_id', '=', $user->id)->update([
            'user_id' => User::SYSTEM_USER_ID,
        ]);

        Post::where('user_id', '=', $user->id)->update([
            'user_id' => User::SYSTEM_USER_ID,
        ]);

        Topic::where('first_post_user_id', '=', $user->id)->update([
            'first_post_user_id' => User::SYSTEM_USER_ID,
        ]);

        Topic::where('last_post_user_id', '=', $user->id)->update([
            'last_post_user_id' => User::SYSTEM_USER_ID,
        ]);

        PrivateMessage::where('sender_id', '=', $user->id)->update([
            'sender_id' => User::SYSTEM_USER_ID,
        ]);

        Participant::where('user_id', '=', $user->id)->delete();
        Message::where('user_id', '=', $user->id)->delete();
        Like::where('user_id', '=', $user->id)->delete();
        Thank::where('user_id', '=', $user->id)->delete();
        Peer::where('user_id', '=', $user->id)->delete();
        History::where('user_id', '=', $user->id)->delete();
        FailedLoginAttempt::where('user_id', '=', $user->id)->delete();

        // Removes all follows for user
        $user->followers()->detach();
        $user->following()->detach();

        // Removes all FL Tokens for user
        foreach (FreeleechToken::where('user_id', '=', $user->id)->get() as $token) {
            $token->delete();
            cache()->forget('freeleech_token:'.$user->id.':'.$token->torrent_id);
        }

        if ($user->delete()) {
            cache()->forget('user:'.$user->passkey);

            Unit3dAnnounce::removeUser($user);

            return to_route('staff.dashboard.index')
                ->with('success', 'Account has been removed');
        }

        return to_route('staff.dashboard.index')
            ->withErrors('Something went wrong!');
    }
}
