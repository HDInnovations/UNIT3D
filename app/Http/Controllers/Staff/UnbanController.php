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
use App\Http\Requests\Staff\StoreUnbanRequest;
use App\Models\Ban;
use App\Models\User;
use App\Notifications\UserBanExpire;
use App\Services\Unit3dAnnounce;
use Illuminate\Support\Carbon;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\BanControllerTest
 */
class UnbanController extends Controller
{
    /**
     * Unban A User (banned -> new_group).
     */
    public function store(StoreUnbanRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($request->integer('owned_by'));
        $staff = $request->user();

        abort_if($user->group->is_modo || $request->user()->is($user), 403);

        $user->update([
            'group_id'     => $request->group_id,
            'can_download' => 1,
        ]);

        Ban::create([
            'owned_by'     => $user->id,
            'created_by'   => $staff->id,
            'unban_reason' => $request->unban_reason,
            'removed_at'   => Carbon::now(),
        ]);

        cache()->forget('user:'.$user->passkey);

        Unit3dAnnounce::addUser($user);

        $user->notify(new UserBanExpire());

        return to_route('users.show', ['user' => $user])
            ->with('success', 'User is now relieved of his ban!');
    }
}
