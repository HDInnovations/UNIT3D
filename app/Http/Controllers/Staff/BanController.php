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

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreBanRequest;
use App\Models\Ban;
use App\Models\Group;
use App\Models\User;
use App\Notifications\UserBan;
use App\Services\Unit3dAnnounce;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\BanControllerTest
 */
class BanController extends Controller
{
    /**
     * Display All Bans.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ban.index', [
            'bans' => Ban::latest()->with('banneduser.group', 'staffuser.group')->paginate(25),
        ]);
    }

    /**
     * Ban A User (current_group -> banned).
     *
     * @throws Exception
     */
    public function store(StoreBanRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($request->string('owned_by'));
        $staff = $request->user();

        abort_if($user->group->is_modo || $staff->is($user), 403);

        $user->update([
            'group_id'     => Group::where('slug', '=', 'banned')->soleValue('id'),
            'can_download' => 0,
        ]);

        $ban = Ban::create(['created_by' => $staff->id] + $request->validated());

        cache()->forget('user:'.$user->passkey);

        Unit3dAnnounce::addUser($user);

        $user->notify(new UserBan($ban));

        return to_route('users.show', ['user' => $user])
            ->with('success', 'User is now banned!');
    }
}
