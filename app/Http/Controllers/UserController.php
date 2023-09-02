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

namespace App\Http\Controllers;

use App\Helpers\Bencode;
use App\Models\Ban;
use App\Models\BonTransactions;
use App\Models\Follow;
use App\Models\Graveyard;
use App\Models\Group;
use App\Models\History;
use App\Models\Invite;
use App\Models\Peer;
use App\Models\Post;
use App\Models\Topic;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserPrivacy;
use App\Models\Warning;
use App\Rules\EmailBlacklist;
use Assada\Achievements\Model\AchievementProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use ZipArchive;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\UserControllerTest
 */
class UserController extends Controller
{
    /**
     * Show A User.
     */
    public function show(string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::with(['privacy', 'history'])
            ->withCount('torrents')
            ->where('username', '=', $username)
            ->when(\auth()->user()->group->is_modo == true, fn ($query) => $query->withTrashed())
            ->firstOrFail();

        $groups = Group::all();
        $followers = Follow::where('target_id', '=', $user->id)->latest()->limit(25)->get();
        $history = $user->history;
        $warnings = Warning::where('user_id', '=', $user->id)->where('active', '=', 1)->take(\config('hitrun.max_warnings'))->get();
        $hitrun = Warning::where('user_id', '=', $user->id)->whereNotNull('torrent')->latest()->paginate(10);

        $bonupload = BonTransactions::where('sender', '=', $user->id)->where([['name', 'like', '%Upload%']])->sum('cost');
        //$bondownload = BonTransactions::where('sender', '=', $user->id)->where([['name', 'like', '%Download%']])->sum('cost');

        //  With Multipliers
        $hisUplCre = History::where('user_id', '=', $user->id)->sum('uploaded');
        //  Without Multipliers
        $hisUpl = History::where('user_id', '=', $user->id)->sum('actual_uploaded');

        $defUpl = \config('other.default_upload');
        $multiUpload = $hisUplCre - $hisUpl;
        $manUpload = $user->uploaded - $hisUplCre - $defUpl - $bonupload;
        $realupload = $user->getUploaded();

        $hisDown = History::where('user_id', '=', $user->id)->sum('actual_downloaded');
        $defDown = \config('other.default_download');
        $freeDownload = $hisDown + $defDown - $user->downloaded;
        $realdownload = $user->getDownloaded();

        $invitedBy = Invite::where('accepted_by', '=', $user->id)->first();

        $requested = TorrentRequest::where('user_id', '=', $user->id)->count();
        $filled = TorrentRequest::where('filled_by', '=', $user->id)->whereNotNull('approved_by')->count();

        $peers = Peer::where('user_id', '=', $user->id)->get();

        $achievements = AchievementProgress::with('details')
            ->where('achiever_id', '=', $user->id)
            ->whereNotNull('unlocked_at')
            ->get();

        return \view('user.profile', [
            'route'        => 'profile',
            'user'         => $user,
            'groups'       => $groups,
            'followers'    => $followers,
            'history'      => $history,
            'warnings'     => $warnings,
            'hitrun'       => $hitrun,

            //'bondownload'  => $bondownload,
            'realdownload' => $realdownload,
            'def_download' => $defDown,
            'his_down'     => $hisDown,
            'free_down'    => $freeDownload,

            'realupload'   => $realupload,
            'def_upload'   => $defUpl,
            'his_upl'      => $hisUpl,
            'multi_upload' => $multiUpload,
            'bonupload'    => $bonupload,
            'man_upload'   => $manUpload,

            'requested'     => $requested,
            'filled'        => $filled,
            'invitedBy'     => $invitedBy,
            'peers'         => $peers,
            'achievements'  => $achievements,
        ]);
    }

    /**
     * User Followers.
     */
    public function followers(string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $results = Follow::with('user')->where('target_id', '=', $user->id)->latest()->paginate(25);

        return \view('user.followers', [
            'route'   => 'follower',
            'results' => $results,
            'user'    => $user,
        ]);
    }

    /**
     * User Topics.
     */
    public function topics(string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $results = Topic::where('topics.first_post_user_id', '=', $user->id)->latest()->paginate(25);

        return \view('user.topics', [
            'route'   => 'forum',
            'results' => $results,
            'user'    => $user,
        ]);
    }

    /**
     * User Posts.
     */
    public function posts(string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $results = Post::selectRaw('posts.id as id,posts.*')->with(['topic', 'user'])->leftJoin('topics', 'posts.topic_id', '=', 'topics.id')->where('posts.user_id', '=', $user->id)->latest('posts.created_at')->paginate(25);

        return \view('user.posts', [
            'route'   => 'forum',
            'results' => $results,
            'user'    => $user,
        ]);
    }

    /**
     * Edit Profile Form.
     */
    public function editProfileForm(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        return \view('user.edit_profile', ['user' => $user, 'route' => 'edit']);
    }

    /**
     * Edit User Profile.
     */
    public function editProfile(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        // Avatar
        $maxUpload = \config('image.max_upload_size');
        if ($request->hasFile('image') && $request->file('image')->getError() === 0) {
            $image = $request->file('image');
            if (\in_array($image->getClientOriginalExtension(), ['jpg', 'JPG', 'jpeg', 'bmp', 'png', 'PNG', 'tiff', 'gif']) && \preg_match('#image/*#', (string) $image->getMimeType())) {
                if ($maxUpload >= $image->getSize()) {
                    $filename = $user->username.'.'.$image->getClientOriginalExtension();
                    $path = \public_path('/files/img/'.$filename);
                    if ($image->getClientOriginalExtension() !== 'gif') {
                        Image::make($image->getRealPath())->fit(150, 150)->encode('png', 100)->save($path);
                    } else {
                        $v = \validator($request->all(), [
                            'image' => 'dimensions:ratio=1/1',
                        ]);
                        if ($v->passes()) {
                            $image->move(\public_path('/files/img/'), $filename);
                        } else {
                            return \to_route('users.show', ['username' => $user->username])
                                ->withErrors('Because you are uploading a GIF, your avatar must be square!');
                        }
                    }

                    $user->image = $user->username.'.'.$image->getClientOriginalExtension();
                } else {
                    return \to_route('users.show', ['username' => $user->username])
                        ->withErrors('Your avatar is too large, max file size: '.($maxUpload / 1_000_000).' MB');
                }
            }
        }

        // Prevent User from abusing BBCODE Font Size (max. 99)
        $aboutTemp = $request->input('about');
        if (\str_contains((string) $aboutTemp, '[size=') && \preg_match('/\[size=[0-9]{3,}\]/', (string) $aboutTemp)) {
            return \to_route('users.show', ['username' => $user->username])
                ->withErrors('Font size is too big!');
        }

        // Define data
        $user->title = $request->input('title');
        $user->about = $request->input('about');
        $user->signature = $request->input('signature');
        $user->save();

        return \to_route('user_edit_profile_form', ['username' => $user->username])
            ->withSuccess('Your Account Was Updated Successfully!');
    }

    /**
     * User Account Settings.
     */
    public function settings(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        return \view('user.settings', ['user' => $user, 'route' => 'settings']);
    }

    /**
     * Change User Account Settings.
     */
    public function changeSettings(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        // General Settings
        $user->censor = $request->input('censor');
        $user->chat_hidden = $request->input('chat_hidden');

        // Language Settings
        $user->locale = $request->input('language');

        // Style Settings
        $user->style = (int) $request->input('theme');

        $customCss = $request->input('custom_css');
        if (isset($customCss) && ! \filter_var($customCss, FILTER_VALIDATE_URL)) {
            return \to_route('users.show', ['username' => $user->username])
                ->withErrors('The URL for the external CSS stylesheet is invalid, try it again with a valid URL.');
        }

        $user->custom_css = $customCss;

        $standaloneCss = $request->input('standalone_css');
        if (isset($standaloneCss) && ! \filter_var($standaloneCss, FILTER_VALIDATE_URL)) {
            return \to_route('users.show', ['username' => $user->username])
                ->withErrors('The URL for the external CSS stylesheet is invalid, try it again with a valid URL.');
        }

        $user->standalone_css = $standaloneCss;

        // Torrent Settings
        $user->torrent_layout = (int) $request->input('torrent_layout');
        $user->show_poster = $request->input('show_poster');
        $user->ratings = $request->input('ratings');
        $user->save();

        return \to_route('user_settings', ['username' => $user->username])
            ->withSuccess('Your Account Was Updated Successfully!');
    }

    /**
     * User Security Settings.
     */
    public function security(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        return \view('user.security', ['user' => $user]);
    }

    /**
     * User TwoStep Auth.
     */
    protected function changeTwoStep(Request $request): \Illuminate\Http\RedirectResponse
    {
        if ($request->getMethod() == 'GET') {
            return \to_route('user_security', ['username' => $request->user()->username]);
        }

        $user = \auth()->user();

        \abort_unless(\config('auth.TwoStepEnabled') == true, 403);
        $user->twostep = $request->input('twostep');
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Changed Your TwoStep Auth Status!');
    }

    /**
     * User Password Change.
     */
    protected function changePassword(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $v = \validator($request->all(), [
            'current_password'          => 'required',
            'new_password'              => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ]);
        if ($v->passes()) {
            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = Hash::make($request->input('new_password'));
                $user->save();

                return \to_route('home.index')->withSuccess('Your Password Has Been Reset');
            }

            return \to_route('user_security', ['username' => $user->username, 'hash' => '#password'])
                ->withErrors('Your Password Was Incorrect!');
        }

        return \to_route('user_security', ['username' => $user->username, 'hash' => '#password'])
                ->withErrors('Your New Password Is To Weak!');
    }

    /**
     * User Email Change.
     */
    protected function changeEmail(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        if (\config('email-blacklist.enabled')) {
            $v = \validator($request->all(), [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:70',
                    'unique:users',
                    new EmailBlacklist(),
                ],
            ]);
        } else {
            $v = \validator($request->all(), [
                'email' => 'required|string|email|max:70|unique:users',
            ]);
        }

        if ($v->fails()) {
            return \to_route('user_security', ['username' => $user->username, 'hash' => '#email'])
                ->withErrors($v->errors());
        }

        $user->email = $request->input('email');
        $user->save();

        return \to_route('user_security', ['username' => $user->username, 'hash' => '#email'])
            ->withSuccess('Your Email Was Updated Successfully!');
    }

    /**
     * Change User Privacy Level.
     */
    public function makePrivate(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->private_profile = 1;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Gone Private!');
    }

    /**
     * Change User Privacy Level.
     */
    public function makePublic(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->private_profile = 0;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Gone Public!');
    }

    /**
     * Change User Notification Setting.
     */
    public function disableNotifications(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->block_notifications = 1;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Disabled Notifications!');
    }

    /**
     * Change User Notification Setting.
     */
    public function enableNotifications(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->block_notifications = 0;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Enabled Notifications!');
    }

    /**
     * Change User Hidden Value.
     */
    public function makeHidden(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->hidden = 1;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Disappeared Like A Ninja!');
    }

    /**
     * Change User Hidden Value.
     */
    public function makeVisible(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->hidden = 0;
        $user->save();

        return \to_route('users.show', ['username' => $user->username])
            ->withSuccess('You Have Given Up Your Ninja Ways And Become Visible!');
    }

    /**
     * Change User PID.
     *
     * @throws \Exception
     */
    public function changePID(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->passkey = \md5(\random_bytes(60).$user->password);
        $user->save();

        \cache()->forget(\sprintf('user:%s', $user->passkey));

        return \to_route('user_security', ['username' => $user->username, 'hash' => '#pid'])
            ->withSuccess('Your PID Was Changed Successfully!');
    }

    /**
     * User Other Privacy Change.
     */
    protected function changeOther(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_other_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_online = ($request->input('show_online') && $request->input('show_online') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#other'])
            ->withSuccess('Your Other Privacy Settings Have Been Saved!');
    }

    /**
     * User Request Privacy Change.
     */
    protected function changeRequest(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_request_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_requested = ($request->input('show_requested') && $request->input('show_requested') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#request'])
            ->withSuccess('Your Request Privacy Settings Have Been Saved!');
    }

    /**
     * User Achievement Privacy Change.
     */
    protected function changeAchievement(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_achievement_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_achievement = ($request->input('show_achievement') && $request->input('show_achievement') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#achievement'])
            ->withSuccess('Your Achievement Privacy Settings Have Been Saved!');
    }

    /**
     * User Forum Privacy Change.
     */
    protected function changeForum(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_forum_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_topic = ($request->input('show_topic') && $request->input('show_topic') == 1 ? 1 : 0);
        $privacy->show_post = ($request->input('show_post') && $request->input('show_post') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#forum'])
            ->withSuccess('Your Forum History Privacy Settings Have Been Saved!');
    }

    /**
     * User Follower Privacy Change.
     */
    protected function changeFollower(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_follower_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_follower = ($request->input('show_follower') && $request->input('show_follower') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#follower'])
            ->withSuccess('Your Follower Privacy Settings Have Been Saved!');
    }

    /**
     * User Torrent Privacy Change.
     */
    protected function changeTorrent(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_torrent_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_upload = ($request->input('show_upload') && $request->input('show_upload') == 1 ? 1 : 0);
        $privacy->show_download = ($request->input('show_download') && $request->input('show_download') == 1 ? 1 : 0);
        $privacy->show_peer = ($request->input('show_peer') && $request->input('show_peer') == 1 ? 1 : 0);
        $privacy->save();

        $user->peer_hidden = 0;
        $user->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#torrent'])
            ->withSuccess('Your Torrent History Privacy Settings Have Been Saved!');
    }

    /**
     * User Account Notification Change.
     */
    protected function changeAccountNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_account_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_account_follow = ($request->input('show_account_follow') && $request->input('show_account_follow') == 1 ? 1 : 0);
        $notification->show_account_unfollow = ($request->input('show_account_unfollow') && $request->input('show_account_unfollow') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#account'])
            ->withSuccess('Your Account Notification Settings Have Been Saved!');
    }

    /**
     * User Following Notification Change.
     */
    protected function changeFollowingNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_following_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_following_upload = ($request->input('show_following_upload') && $request->input('show_following_upload') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#following'])
            ->withSuccess('Your Followed User Notification Settings Have Been Saved!');
    }

    /**
     * User BON Notification Change.
     */
    protected function changeBonNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_bon_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_bon_gift = ($request->input('show_bon_gift') && $request->input('show_bon_gift') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#bon'])
            ->withSuccess('Your BON Notification Settings Have Been Saved!');
    }

    /**
     * User Subscription Notification Change.
     */
    protected function changeSubscriptionNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_subscription_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_subscription_forum = ($request->input('show_subscription_forum') && $request->input('show_subscription_forum') == 1 ? 1 : 0);
        $notification->show_subscription_topic = ($request->input('show_subscription_topic') && $request->input('show_subscription_topic') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#subscription'])
            ->withSuccess('Your Subscription Notification Settings Have Been Saved!');
    }

    /**
     * User Request Notification Change.
     */
    protected function changeRequestNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_request_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_request_comment = ($request->input('show_request_comment') && $request->input('show_request_comment') == 1 ? 1 : 0);
        $notification->show_request_bounty = ($request->input('show_request_bounty') && $request->input('show_request_bounty') == 1 ? 1 : 0);
        $notification->show_request_fill = ($request->input('show_request_fill') && $request->input('show_request_fill') == 1 ? 1 : 0);
        $notification->show_request_fill_approve = ($request->input('show_request_fill_approve') && $request->input('show_request_fill_approve') == 1 ? 1 : 0);
        $notification->show_request_fill_reject = ($request->input('show_request_fill_reject') && $request->input('show_request_fill_reject') == 1 ? 1 : 0);
        $notification->show_request_claim = ($request->input('show_request_claim') && $request->input('show_request_claim') == 1 ? 1 : 0);
        $notification->show_request_unclaim = ($request->input('show_request_unclaim') && $request->input('show_request_unclaim') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#request'])
            ->withSuccess('Your Request Notification Settings Have Been Saved!');
    }

    /**
     * User Torrent Notification Change.
     */
    protected function changeTorrentNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_torrent_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_torrent_comment = ($request->input('show_torrent_comment') && $request->input('show_torrent_comment') == 1 ? 1 : 0);
        $notification->show_torrent_thank = ($request->input('show_torrent_thank') && $request->input('show_torrent_thank') == 1 ? 1 : 0);
        $notification->show_torrent_tip = ($request->input('show_torrent_tip') && $request->input('show_torrent_tip') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#torrent'])
            ->withSuccess('Your Torrent Notification Settings Have Been Saved!');
    }

    /**
     * User Mention Notification Change.
     */
    protected function changeMentionNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_mention_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_mention_torrent_comment = ($request->input('show_mention_torrent_comment') && $request->input('show_mention_torrent_comment') == 1 ? 1 : 0);
        $notification->show_mention_request_comment = ($request->input('show_mention_request_comment') && $request->input('show_mention_request_comment') == 1 ? 1 : 0);
        $notification->show_mention_article_comment = ($request->input('show_mention_article_comment') && $request->input('show_mention_article_comment') == 1 ? 1 : 0);
        $notification->show_mention_forum_post = ($request->input('show_mention_forum_post') && $request->input('show_mention_forum_post') == 1 ? 1 : 0);

        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#mention'])
            ->withSuccess('Your @Mention Notification Settings Have Been Saved!');
    }

    /**
     * User Forum Notification Change.
     */
    protected function changeForumNotification(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $notification = $user->notification;
        if (! $notification) {
            $notification = new UserNotification();
            $notification->setDefaultValues();
            $notification->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $notification->json_forum_groups = \array_merge($notification->expected_groups, ['default_groups' => $tomerge]);
        $notification->show_forum_topic = ($request->input('show_forum_topic') && $request->input('show_forum_topic') == 1 ? 1 : 0);
        $notification->save();

        return \to_route('user_notification', ['username' => $user->username, 'hash' => '#forum'])
            ->withSuccess('Your Forum Notification Settings Have Been Saved!');
    }

    /**
     * User Profile Privacy Change.
     */
    protected function changeProfile(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $privacy = $user->privacy;
        if (! $privacy) {
            $privacy = new UserPrivacy();
            $privacy->setDefaultValues();
            $privacy->user_id = $user->id;
        }

        $approved = $request->input('approved');
        $groups = Group::all();
        $tomerge = [];
        foreach ($groups as $group) {
            $tomerge[$group->id] = \is_array($approved) && \in_array($group->id, $approved) ? 1 : 0;
        }

        $privacy->json_profile_groups = \array_merge($privacy->expected_groups, ['default_groups' => $tomerge]);
        $privacy->show_profile_torrent_count = ($request->input('show_profile_torrent_count') && $request->input('show_profile_torrent_count') == 1 ? 1 : 0);
        $privacy->show_profile_torrent_ratio = ($request->input('show_profile_torrent_ratio') && $request->input('show_profile_torrent_ratio') == 1 ? 1 : 0);
        $privacy->show_profile_torrent_seed = ($request->input('show_profile_torrent_seed') && $request->input('show_profile_torrent_seed') == 1 ? 1 : 0);
        $privacy->show_profile_torrent_extra = ($request->input('show_profile_torrent_extra') && $request->input('show_profile_torrent_extra') == 1 ? 1 : 0);
        $privacy->show_profile_about = ($request->input('show_profile_about') && $request->input('show_profile_about') == 1 ? 1 : 0);
        $privacy->show_profile_achievement = ($request->input('show_profile_achievement') && $request->input('show_profile_achievement') == 1 ? 1 : 0);
        $privacy->show_profile_badge = ($request->input('show_profile_badge') && $request->input('show_profile_badge') == 1 ? 1 : 0);
        $privacy->show_profile_follower = ($request->input('show_profile_follower') && $request->input('show_profile_follower') == 1 ? 1 : 0);
        $privacy->show_profile_title = ($request->input('show_profile_title') && $request->input('show_profile_title') == 1 ? 1 : 0);
        $privacy->show_profile_bon_extra = ($request->input('show_profile_bon_extra') && $request->input('show_profile_bon_extra') == 1 ? 1 : 0);
        $privacy->show_profile_comment_extra = ($request->input('show_profile_comment_extra') && $request->input('show_profile_comment_extra') == 1 ? 1 : 0);
        $privacy->show_profile_forum_extra = ($request->input('show_profile_forum_extra') && $request->input('show_profile_forum_extra') == 1 ? 1 : 0);
        $privacy->show_profile_request_extra = ($request->input('show_profile_request_extra') && $request->input('show_profile_request_extra') == 1 ? 1 : 0);
        $privacy->show_profile_warning = ($request->input('show_profile_warning') && $request->input('show_profile_warning') == 1 ? 1 : 0);
        $privacy->save();

        return \to_route('user_privacy', ['username' => $user->username, 'hash' => '#profile'])
            ->withSuccess('Your Profile Privacy Settings Have Been Saved!');
    }

    /**
     * Change User RID.
     */
    public function changeRID(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->rsskey = \md5(\random_bytes(60).$user->password);
        $user->save();

        return \to_route('user_security', ['username' => $user->username, 'hash' => '#rid'])
            ->withSuccess('Your RID Was Changed Successfully!');
    }

    /**
     * Change User API Token.
     */
    public function changeApiToken(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $user->api_token = Str::random(100);
        $user->save();

        return \to_route('user_security', ['username' => $user->username, 'hash' => '#api'])
            ->withSuccess('Your API Token Was Changed Successfully!');
    }

    /**
     * User Privacy Settings.
     */
    public function privacy(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $groups = Group::where('level', '>', 0)->latest('level')->get();

        return \view('user.privacy', ['user' => $user, 'groups' => $groups]);
    }

    /**
     * User Notification Settings.
     */
    public function notification(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->id == $user->id, 403);

        $groups = Group::where('level', '>', 0)->latest('level')->get();

        return \view('user.notification', ['user' => $user, 'groups' => $groups]);
    }

    /**
     * Uses Input's To Put Together A Filtered View.
     *
     * @throws \Throwable
     */
    public function myFilter(Request $request, string $username): string|bool
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->group->is_modo || $request->user()->id == $user->id, 403);
        if ($request->has('view') && $request->input('view') == 'requests') {
            $torrentRequests = TorrentRequest::with(['user', 'category', 'type']);
            $order = null;
            $sorting = null;
            if ($request->has('name') && $request->input('name') != null) {
                $torrentRequests->where('name', 'like', '%'.$request->input('name').'%');
            }

            if ($request->has('filled') && $request->input('filled') != null) {
                $torrentRequests->whereNotNull('filled_by')
                    ->whereNotNull('filled_hash')
                    ->whereNotNull('filled_when')
                    ->whereNotNull('approved_by')
                    ->whereNotNull('approved_when');
            }

            if ($request->has('pending') && $request->input('pending') != null) {
                $torrentRequests->whereNotNull('filled_by')
                    ->whereNotNull('filled_hash')
                    ->whereNotNull('filled_when')
                    ->whereNull('approved_by')
                    ->whereNull('approved_when');
            }

            if ($request->has('claimed') && $request->input('claimed') != null) {
                $torrentRequests->where('claimed', '=', 1);
            }

            if ($request->has('unfilled') && $request->input('unfilled') != null) {
                $torrentRequests->where(function ($query) {
                    $query->whereNull('filled_by')
                        ->orWhereNull('filled_hash')
                        ->orWhereNull('approved_by');
                });
            }

            if ($request->has('sorting') && $request->input('sorting') != null) {
                $sorting = $request->input('sorting');
            }

            if ($request->has('direction') && $request->input('direction') != null) {
                $order = $request->input('direction');
            }

            if (! $sorting || ! $order) {
                $sorting = 'created_at';
                $order = 'desc';
                // $order = 'asc';
            }

            $direction = $order == 'asc' ? 1 : 2;
            if ($sorting == 'date') {
                $table = $torrentRequests->where('user_id', '=', $user->id)
                    ->orderBy('created_at', $order)
                    ->paginate(25);
            } else {
                $table = $torrentRequests->where('user_id', '=', $user->id)
                    ->orderBy($sorting, $order)
                    ->paginate(25);
            }

            return \view('user.filters.requests', [
                'user'            => $user,
                'torrentRequests' => $table,
            ])->render();
        }

        if ($request->has('view') && $request->input('view') == 'resurrections') {
            $history = Graveyard::with(['torrent', 'user'])
                ->leftJoin('torrents', 'torrents.id', '=', 'graveyard.torrent_id');
            $order = null;
            $sorting = null;
            if ($request->has('rewarded') && $request->input('rewarded') != null) {
                $history->where('graveyard.rewarded', '=', 1);
            }

            if ($request->has('notrewarded') && $request->input('notrewarded') != null) {
                $history->where('graveyard.rewarded', '=', 0);
            }

            if ($request->has('name') && $request->input('name') != null) {
                $history->where('torrents.name', 'like', '%'.$request->input('name').'%');
            }

            if ($request->has('sorting') && $request->input('sorting') != null) {
                $sorting = $request->input('sorting');
            }

            if ($request->has('direction') && $request->input('direction') != null) {
                $order = $request->input('direction');
            }

            if (! $sorting || ! $order) {
                $sorting = 'created_at';
                $order = 'desc';
                // $order = 'asc';
            }

            $direction = $order == 'asc' ? 1 : 2;
            if ($sorting != 'name' && $sorting != 'size' && $sorting != 'times_completed' && $sorting != 'seeders' && $sorting != 'leechers') {
                if ($sorting == 'goal') {
                    $table = $history->where('graveyard.user_id', '=', $user->id)
                        ->orderBy('graveyard.seedtime', $order)
                        ->paginate(50);
                } else {
                    $table = $history->where('graveyard.user_id', '=', $user->id)
                        ->orderBy('graveyard.'.$sorting, $order)
                        ->paginate(50);
                }
            } else {
                $table = $history->where('graveyard.user_id', '=', $user->id)
                    ->orderBy('torrents.'.$sorting, $order)
                    ->paginate(50);
            }

            return \view('user.filters.resurrections', [
                'user'          => $user,
                'resurrections' => $table,
            ])->render();
        }

        return false;
    }

    /**
     * Get A Users Requested Table.
     */
    public function requested(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        if (($request->user()->id == $user->id || $request->user()->group->is_modo)) {
            $logger = 'user.private.requests';

            $torrentRequests = TorrentRequest::with(['user', 'category', 'type'])->where('user_id', '=', $user->id)->latest()->paginate(25);

            return \view($logger, [
                'route'           => 'requests',
                'user'            => $user,
                'torrentRequests' => $torrentRequests,
            ]);
        }

        $logger = 'user.requests';
        $torrentRequests = TorrentRequest::with(['user', 'category', 'type'])->where('user_id', '=', $user->id)->where('anon', '!=', 1)->latest()->paginate(25);

        return \view($logger, [
            'route'           => 'requests',
            'user'            => $user,
            'torrentRequests' => $torrentRequests,
        ]);
    }

    /**
     * Get A Users History Table.
     */
    public function torrents(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->group->is_modo || $request->user()->id == $user->id, 403);
        $hisUpl = History::where('user_id', '=', $user->id)->sum('actual_uploaded');
        $hisUplCre = History::where('user_id', '=', $user->id)->sum('uploaded');
        $hisDownl = History::where('user_id', '=', $user->id)->sum('actual_downloaded');
        $hisDownlCre = History::where('user_id', '=', $user->id)->sum('downloaded');

        return \view('user.private.torrents', [
            'route'         => 'torrents',
            'user'          => $user,
            'his_upl'       => $hisUpl,
            'his_upl_cre'   => $hisUplCre,
            'his_downl'     => $hisDownl,
            'his_downl_cre' => $hisDownlCre,
        ]);
    }

    /**
     * Get A Users Uploads Table.
     */
    public function uploads(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->group->is_modo || $request->user()->id == $user->id, 403);
        $hisUpl = History::where('user_id', '=', $user->id)->sum('actual_uploaded');
        $hisUplCre = History::where('user_id', '=', $user->id)->sum('uploaded');
        $hisDownl = History::where('user_id', '=', $user->id)->sum('actual_downloaded');
        $hisDownlCre = History::where('user_id', '=', $user->id)->sum('downloaded');

        return \view('user.private.uploads', [
            'route'         => 'uploads',
            'user'          => $user,
            'his_upl'       => $hisUpl,
            'his_upl_cre'   => $hisUplCre,
            'his_downl'     => $hisDownl,
            'his_downl_cre' => $hisDownlCre,
        ]);
    }

    /**
     * Get A Users Graveyard Resurrections.
     */
    public function resurrections(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        \abort_unless($request->user()->group->is_modo || $request->user()->id == $user->id, 403);

        $resurrections = Graveyard::with(['torrent', 'user'])->where('user_id', '=', $user->id)->paginate(50);

        return \view('user.private.resurrections', [
            'route'         => 'resurrections',
            'user'          => $user,
            'resurrections' => $resurrections,
        ]);
    }

    /**
     * Get A Users Active Table.
     */
    public function active(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless($request->user()->group->is_modo || $request->user()->id == $user->id, 403);

        $hisUpl = History::where('user_id', '=', $user->id)->sum('actual_uploaded');
        $hisUplCre = History::where('user_id', '=', $user->id)->sum('uploaded');
        $hisDownl = History::where('user_id', '=', $user->id)->sum('actual_downloaded');
        $hisDownlCre = History::where('user_id', '=', $user->id)->sum('downloaded');

        return \view('user.private.active', ['user' => $user,
            'route'                                 => 'active',
            'his_upl'                               => $hisUpl,
            'his_upl_cre'                           => $hisUplCre,
            'his_downl'                             => $hisDownl,
            'his_downl_cre'                         => $hisDownlCre,
        ]);
    }

    /**
     * Get A Users Bans.
     */
    public function getBans(Request $request, string $username): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        \abort_unless($request->user()->group->is_modo, 403);

        $user = User::where('username', '=', $username)->firstOrFail();
        $bans = Ban::where('owned_by', '=', $user->id)->latest()->get();

        return \view('user.banlog', [
            'user'      => $user,
            'bans'      => $bans,
        ]);
    }

    /**
     * Download All History Torrents.
     */
    public function downloadHistoryTorrents(Request $request, string $username): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        //  Extend The Maximum Execution Time
        \set_time_limit(1200);

        // Authorized User
        $user = User::where('username', '=', $username)->firstOrFail();
        \abort_unless($request->user()->id == $user->id, 403);

        // Define Dir For Zip
        $zipPath = \getcwd().'/files/tmp_zip/';

        // Check Directory exists
        if (! File::isDirectory($zipPath)) {
            File::makeDirectory($zipPath, 0755, true, true);
        }

        // Zip File Name
        $zipFileName = \sprintf('%s.zip', $user->username);

        // Create ZipArchive Obj
        $zipArchive = new ZipArchive();

        // Get Users History
        $historyTorrents = History::whereHas('torrent')
            ->where('user_id', '=', $user->id)
            ->pluck('torrent_id');

        if ($zipArchive->open($zipPath.'/'.$zipFileName, ZipArchive::CREATE) === true) {
            // Match History Results To Torrents
            $failCSV = '"Name","URL","ID","info_hash"
';
            $failCount = 0;
            foreach ($historyTorrents as $historyTorrent) {
                // Get Torrent
                $torrent = Torrent::withAnyStatus()
                    ->where('id', '=', $historyTorrent)
                    ->first();

                // Define The Torrent Filename
                $tmpFileName = \sprintf('%s.torrent', $torrent->slug);

                // The Torrent File Exist?
                if (! \file_exists(\getcwd().'/files/torrents/'.$torrent->file_name)) {
                    $failCSV .= '"'.$torrent->name.'","'.\route('torrent', ['id' => $torrent->id]).'","'.$torrent->id.'","'.$torrent->info_hash.'"
';
                    $failCount++;
                } else {
                    // Delete The Last Torrent Tmp File If Exist
                    if (\file_exists(\getcwd().'/files/tmp/'.$tmpFileName)) {
                        \unlink(\getcwd().'/files/tmp/'.$tmpFileName);
                    }

                    // Get The Content Of The Torrent
                    $dict = Bencode::bdecode(\file_get_contents(\getcwd().'/files/torrents/'.$torrent->file_name));
                    // Set the announce key and add the user passkey
                    $dict['announce'] = \route('announce', ['passkey' => $user->passkey]);
                    // Remove Other announce url
                    unset($dict['announce-list']);

                    $fileToDownload = Bencode::bencode($dict);
                    \file_put_contents(\getcwd().'/files/tmp/'.$tmpFileName, $fileToDownload);

                    // Add Files To ZipArchive
                    $zipArchive->addFile(\getcwd().'/files/tmp/'.$tmpFileName, $tmpFileName);
                }
            }

            if ($failCount > 0) {
                $CSVtmpName = \sprintf('%s.zip', $user->username).'-missingTorrentFiles.CSV';
                \file_put_contents(\getcwd().'/files/tmp/'.$CSVtmpName, $failCSV);
                $zipArchive->addFile(\getcwd().'/files/tmp/'.$CSVtmpName, 'missingTorrentFiles.CSV');
            }

            // Close ZipArchive
            $zipArchive->close();
        }

        $zipFile = $zipPath.'/'.$zipFileName;

        if (\file_exists($zipFile)) {
            return \response()->download($zipFile)->deleteFileAfterSend(true);
        }

        return \redirect()->back()->withErrors('Something Went Wrong!');
    }

    /**
     * Accept Site Rules.
     */
    public function acceptRules(Request $request): void
    {
        $user = $request->user();
        $user->read_rules = 1;
        $user->save();
    }

    /**
     * Flushes own Peers.
     */
    public function flushOwnGhostPeers(Request $request, string $username): \Illuminate\Http\RedirectResponse
    {
        // Authorized User
        $user = User::where('username', '=', $username)->firstOrFail();
        \abort_unless($request->user()->id == $user->id, 403);

        // Check if User can flush
        if ($request->user()->own_flushes == 0) {
            return \redirect()->back()->withErrors('You can only flush twice a day!');
        }

        $carbon = new Carbon();

        // Get Peer List from User
        $peers = Peer::select(['id', 'torrent_id', 'user_id', 'updated_at'])
            ->where('user_id', '=', $user->id)
            ->where('updated_at', '<', $carbon->copy()->subMinutes(70)->toDateTimeString())
            ->get();

        // Return with Error if no Peer exists
        if ($peers->isEmpty()) {
            return \redirect()->back()->withErrors('No Peers found! Please wait at least 70 Minutes after the last announce from the client!');
        }

        $new_value = $user->own_flushes - 1;
        User::where('username', '=', $username)->update(['own_flushes' => $new_value]);

        // Iterate over Peers
        foreach ($peers as $peer) {
            $history = History::where('torrent_id', '=', $peer->torrent_id)
                ->where('user_id', '=', $peer->user_id)
                ->first();
            if ($history) {
                $history->active = false;
                $history->save();
            }

            $peer->delete();
        }

        return \redirect()->back()->withSuccess('Peers were flushed successfully!');
    }
}
