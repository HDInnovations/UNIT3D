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

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\BonTransactions;
use App\Models\Donation;
use App\Models\Invite;
use App\Models\Peer;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Assada\Achievements\Model\AchievementProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\UserControllerTest
 */
class UserController extends Controller
{
    /**
     * Show A User.
     */
    public function show(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user->load([
            'application',
            'privacy',
            'userban' => ['banneduser', 'staffuser'],
            'tickets' => fn ($query) => $query->orderByRaw('CASE WHEN closed_at IS NULL THEN 1 ELSE 0 END DESC')->orderByDesc('id'),
        ])
            ->loadCount([
                'torrents as anon_uploads_count'     => fn ($query) => $query->where('anon', '=', true),
                'torrents as non_anon_uploads_count' => fn ($query) => $query->where('anon', '=', false),
                'topics',
                'posts',
                'filledRequests' => fn ($query) => $query->whereNotNull('approved_by'),
                'requests',
                'userwarning as active_warnings_count'       => fn ($query) => $query->where('active', '=', 1),
                'userwarning as auto_warnings_count'         => fn ($query) => $query->whereNotNull('torrent'),
                'userwarning as manual_warnings_count'       => fn ($query) => $query->whereNull('torrent'),
                'userwarning as soft_deleted_warnings_count' => fn ($query) => $query->onlyTrashed(),
            ]);

        return view('user.profile.show', [
            'user'      => $user,
            'followers' => $user->followers()->latest()->limit(25)->get(),
            'history'   => DB::table('history')
                ->where('user_id', '=', $user->id)
                ->where('created_at', '>', $user->created_at)
                ->selectRaw('SUM(actual_uploaded) as upload_sum')
                ->selectRaw('SUM(uploaded) as credited_upload_sum')
                ->selectRaw('SUM(actual_downloaded) as download_sum')
                ->selectRaw('SUM(downloaded) as credited_download_sum')
                ->selectRaw('SUM(refunded_download) as refunded_download_sum')
                ->selectRaw('SUM(seedtime) as seedtime_sum')
                ->selectRaw('SUM(actual_downloaded > 0) as download_count')
                ->selectRaw('COUNT(*) as count')
                ->first(),
            'manualWarnings' => $user
                ->userwarning()
                ->whereNull('torrent')
                ->latest()
                ->paginate(10, ['*'], 'manualWarningsPage'),
            'autoWarnings' => $user
                ->userwarning()
                ->whereNotNull('torrent')
                ->latest()
                ->paginate(10, ['*'], 'autoWarningsPage'),
            'softDeletedWarnings' => $user
                ->userwarning()
                ->onlyTrashed()
                ->with(['torrenttitle', 'warneduser'])
                ->latest('created_at')
                ->paginate(10, ['*'], 'deletedWarningsPage'),
            'boughtUpload' => BonTransactions::where('sender_id', '=', $user->id)->where([['name', 'like', '%Upload%']])->sum('cost'),
            // 'boughtDownload'        => BonTransactions::where('sender_id', '=', $user->id)->where([['name', 'like', '%Download%']])->sum('cost'),
            'invitedBy' => Invite::where('accepted_by', '=', $user->id)->first(),
            'clients'   => $user->peers()
                ->join('torrents', 'torrents.id', '=', 'peers.torrent_id')
                ->select('agent', 'port')
                ->selectRaw('INET6_NTOA(peers.ip) as ip')
                ->selectRaw('MIN(peers.created_at) as created_at')
                ->selectRaw('MAX(peers.updated_at) as updated_at')
                ->selectRaw('SUM(torrents.size) as size')
                ->selectRaw('COUNT(*) as num_peers')
                ->selectRaw('MAX(peers.connectable) as connectable')
                ->groupBy(['ip', 'port', 'agent'])
                ->where('active', '=', true)
                ->get(),
            'achievements' => AchievementProgress::with('details')
                ->where('achiever_id', '=', $user->id)
                ->whereNotNull('unlocked_at')
                ->get(),
            'peers' => Peer::query()
                ->selectRaw('SUM(seeder = FALSE AND active = TRUE) as leeching')
                ->selectRaw('SUM(seeder = TRUE AND active = TRUE) as seeding')
                ->selectRaw('SUM(active = FALSE) as inactive')
                ->where('user_id', '=', $user->id)
                ->first(),
            'watch'        => $user->watchlist,
            'externalUser' => ! $user->trashed() && $request->user()->group->is_modo ? Unit3dAnnounce::getUser($user->id) : false,
            'donation'     => Donation::where('status', '=', ModerationStatus::APPROVED)->where('user_id', '=', $user->id)->latest()->first(),
        ]);
    }

    /**
     * Edit Profile Form.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Edit User Profile.
     */
    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            abort_if(\is_array($image), 400);

            abort_unless($image->getError() === UPLOAD_ERR_OK, 500);

            if (!\in_array($image->getClientOriginalExtension(), ['jpg', 'JPG', 'jpeg', 'bmp', 'png', 'PNG', 'tiff', 'gif'])) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Only .jpg, .bmp, .png, .tiff, and .gif are allowed.');
            }

            if (!preg_match('#image/*#', (string) $image->getMimeType())) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Incorrect mime type.');
            }

            if ($image->getSize() > config('image.max_upload_size')) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Your avatar is too large, max file size: '.(config('image.max_upload_size') / 1_000_000).' MB');
            }

            $filename = $user->username.'.'.$image->getClientOriginalExtension();
            $path = Storage::disk('user-avatars')->path($filename);

            if ($image->getClientOriginalExtension() !== 'gif') {
                Image::make($image->getRealPath())->fit(150, 150)->encode('png', 100)->save($path);
            } else {
                Validator::make($request->all(), [
                    'image' => 'required|dimensions:ratio=1/1',
                ], [
                    'image.dimensions' => 'Only square avatars are accepted.',
                ])->validate();

                $image->storeAs('', $filename, 'user-avatars');
            }

            $avatar = $user->username.'.'.$image->getClientOriginalExtension();

            if ($user->image !== $avatar) {
                $oldAvatar = $user->image;
                $user->image = $avatar;
            }
        }

        if (($request->hasFile('icon') && $user->is_lifetime) || ($request->hasFile('icon') && $user->group->is_modo)) {
            $image = $request->file('icon');

            abort_if(\is_array($image), 400);

            abort_unless($image->getError() === UPLOAD_ERR_OK, 500);

            if (!\in_array($image->getClientOriginalExtension(), ['jpg', 'JPG', 'jpeg', 'bmp', 'png', 'PNG', 'tiff', 'gif'])) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Only .jpg, .bmp, .png, .tiff, and .gif are allowed.');
            }

            if (!preg_match('#image/*#', (string) $image->getMimeType())) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Incorrect mime type.');
            }

            if ($image->getSize() > config('image.max_upload_size')) {
                return to_route('users.show', ['user' => $user])
                    ->withErrors('Your icon is too large, max file size: '.(config('image.max_upload_size') / 1_000_000).' MB');
            }

            $filename = uniqid('', true).'_icon.'.$image->getClientOriginalExtension();
            $path = Storage::disk('user-icons')->path($filename);

            if ($image->getClientOriginalExtension() !== 'gif') {
                Image::make($image->getRealPath())->fit(30, 30)->encode('png', 100)->save($path);
            } else {
                $request->validate([
                    'image' => 'dimensions:ratio=1/1',
                ]);

                $image->storeAs('', $filename, 'user-icons');
            }

            if ($user->icon !== $filename) {
                $oldIcon = $user->icon;
                $user->icon = $filename;
            }
        }

        // Define data
        $request->validate([
            'title'     => 'nullable|max:255',
            'about'     => 'nullable|max:20000',
            'signature' => 'nullable|max:1000'
        ]);
        $user->title = $request->input('title');
        $user->about = $request->input('about');
        $user->signature = $request->input('signature');
        $user->save();

        // Remove avatar's old file format
        if (isset($oldAvatar)) {
            Storage::disk('user-avatars')->delete($oldAvatar);
        }

        // Remove icon's old file format
        if (isset($oldIcon)) {
            Storage::disk('user-icons')->delete($oldIcon);
        }

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Your account was updated successfully!');
    }

    /**
     * Accept Site Rules.
     */
    public function acceptRules(Request $request, User $user): void
    {
        abort_unless($request->user()->is($user), 403);

        $user->update([
            'read_rules' => true,
        ]);
    }
}
