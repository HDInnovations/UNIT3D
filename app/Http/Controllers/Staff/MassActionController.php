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
use App\Models\Group;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\MassActionControllerTest
 */
class MassActionController extends Controller
{
    /**
     * Mass Validate Unvalidated Users.
     *
     * @throws Exception
     */
    public function update(): \Illuminate\Http\RedirectResponse
    {
        $validatingGroupId = Group::where('slug', '=', 'validating')->soleValue('id');
        $memberGroupId = Group::where('slug', '=', 'user')->soleValue('id');

        foreach (User::where('group_id', '=', $validatingGroupId)->get() as $user) {
            $user->update([
                'group_id'          => $memberGroupId,
                'can_download'      => 1,
                'email_verified_at' => now(),
            ]);

            cache()->forget('user:'.$user->passkey);

            Unit3dAnnounce::addUser($user);
        }

        return to_route('staff.dashboard.index')
            ->with('success', 'Unvalidated accounts are now validated');
    }
}
