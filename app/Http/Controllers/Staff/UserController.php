<?php
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

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Group;
use App\Models\Invite;
use App\Models\Like;
use App\Models\Message;
use App\Models\Note;
use App\Models\Peer;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\Thank;
use App\Models\Topic;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\UserControllerTest
 */
class UserController extends Controller
{
    /**
     * @var string[]
     */
    private const WEIGHTS = [
        'is_modo',
        'is_admin',
    ];

    /**
     * Users List.
     */
    public function index(): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        return \view('Staff.user.index');
    }

    /**
     * User Edit Form.
     *
     * @param \App\Models\User $username
     */
    public function settings($username): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $groups = Group::all();
        $notes = Note::where('user_id', '=', $user->id)->latest()->paginate(25);

        return \view('Staff.user.edit', [
            'user'   => $user,
            'groups' => $groups,
            'notes'  => $notes,
        ]);
    }

    /**
     * Edit A User.
     *
     * @param \App\Models\User $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $username)
    {
        $user = User::with('primaryRole')->where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        $sendto = (int) $request->input('role_id');

        // Hard coded until group change.

        if (! $staff->hasPrivilegeTo('users_edit_personal') && ! ($staff->primaryRole->position > $user->primaryRole->position || $staff->hasRole('root') || $staff->hasRole('sudo'))) {
            return \redirect()->route('users.show', ['username' => $user->username])
                ->withErrors('You Are Not Authorized To Perform This Action!');
        }

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->uploaded = $request->input('uploaded');
        $user->downloaded = $request->input('downloaded');
        $user->title = $request->input('title');
        $user->about = $request->input('about');
        $user->role_id = (int) $request->input('role_id');
        $user->save();

        return \redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Was Updated Successfully!');
    }

    /**
     * Edit A Users Permissions.
     *
     * @param \App\Models\User $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permissions(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        if (! $staff->hasPrivilegeTo('users_edit_privileges') && ! ($staff->primaryRole->position > $user->primaryRole->position || $staff->hasRole('root') || $staff->hasRole('sudo'))) {
            return \redirect()->route('users.show', ['username' => $user->username])
                ->withErrors('You Are Not Authorized To Perform This Action!');
        }

        $user->can_upload = $request->input('can_upload');
        $user->can_download = $request->input('can_download');
        $user->can_comment = $request->input('can_comment');
        $user->can_invite = $request->input('can_invite');
        $user->can_request = $request->input('can_request');
        $user->can_chat = $request->input('can_chat');
        $user->save();

        return \redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Permissions Successfully Edited');
    }

    /**
     * Edit A Users Password.
     *
     * @param \App\Models\User $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function password(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        if (! $staff->hasPrivilegeTo('users_edit_security') && ! ($staff->primaryRole->position > $user->primaryRole->position || $staff->hasRole('root') || $staff->hasRole('sudo'))) {
            return \redirect()->route('users.show', ['username' => $user->username])
                ->withErrors('You Are Not Authorized To Perform This Action!');
        }

        $newPassword = $request->input('new_password');
        $user->password = Hash::make($newPassword);
        $user->save();

        return \redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Password Was Updated Successfully!');
    }

    /**
     * Delete A User.
     *
     * @param \App\Models\User $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function destroy(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        \abort_if($user->primaryRole->position >= 900 || $staff->id == $user->id, 403);

        // Removes UserID from Torrents if any and replaces with System UserID (1)
        foreach (Torrent::withAnyStatus()->where('user_id', '=', $user->id)->get() as $tor) {
            $tor->user_id = 1;
            $tor->save();
        }
        // Removes UserID from Comments if any and replaces with System UserID (1)
        foreach (Comment::where('user_id', '=', $user->id)->get() as $com) {
            $com->user_id = 1;
            $com->save();
        }
        // Removes UserID from Posts if any and replaces with System UserID (1)
        foreach (Post::where('user_id', '=', $user->id)->get() as $post) {
            $post->user_id = 1;
            $post->save();
        }
        // Removes UserID from Topic Creators if any and replaces with System UserID (1)
        foreach (Topic::where('first_post_user_id', '=', $user->id)->get() as $topic) {
            $topic->first_post_user_id = 1;
            $topic->save();
        }
        // Removes UserID from Topic if any and replaces with System UserID (1)
        foreach (Topic::where('last_post_user_id', '=', $user->id)->get() as $topic) {
            $topic->last_post_user_id = 1;
            $topic->save();
        }
        // Removes UserID from PM if any and replaces with System UserID (1)
        foreach (PrivateMessage::where('sender_id', '=', $user->id)->get() as $sent) {
            $sent->sender_id = 1;
            $sent->save();
        }
        // Removes UserID from PM if any and replaces with System UserID (1)
        foreach (PrivateMessage::where('receiver_id', '=', $user->id)->get() as $received) {
            $received->receiver_id = 1;
            $received->save();
        }
        // Removes all Posts made by User from the shoutbox
        foreach (Message::where('user_id', '=', $user->id)->get() as $shout) {
            $shout->delete();
        }
        // Removes all notes for user
        foreach (Note::where('user_id', '=', $user->id)->get() as $note) {
            $note->delete();
        }
        // Removes all likes for user
        foreach (Like::where('user_id', '=', $user->id)->get() as $like) {
            $like->delete();
        }
        // Removes all thanks for user
        foreach (Thank::where('user_id', '=', $user->id)->get() as $thank) {
            $thank->delete();
        }
        // Removes all follows for user
        foreach (Follow::where('user_id', '=', $user->id)->get() as $follow) {
            $follow->delete();
        }
        // Removes UserID from Sent Invites if any and replaces with System UserID (1)
        foreach (Invite::where('user_id', '=', $user->id)->get() as $sentInvite) {
            $sentInvite->user_id = 1;
            $sentInvite->save();
        }
        // Removes UserID from Received Invite if any and replaces with System UserID (1)
        foreach (Invite::where('accepted_by', '=', $user->id)->get() as $receivedInvite) {
            $receivedInvite->accepted_by = 1;
            $receivedInvite->save();
        }
        // Removes all Peers for user
        foreach (Peer::where('user_id', '=', $user->id)->get() as $peer) {
            $peer->delete();
        }

        if ($user->delete()) {
            return \redirect()->route('staff.dashboard.index')
                ->withSuccess('Account Has Been Removed');
        }

        return \redirect()->route('staff.dashboard.index')
            ->withErrors('Something Went Wrong!');
    }
}
