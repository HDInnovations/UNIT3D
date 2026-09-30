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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGeneralSettingRequest;
use App\Models\User;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    /**
     * Update user general settings.
     */
    public function update(UpdateGeneralSettingRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $user->settings()->upsert($request->validated(), ['user_id']);

        cache()->forget('user-settings:by-user-id:'.$user->id);

        return to_route('users.general_settings.edit', ['user' => $user])
            ->with('success', __('application-messages.flash.general-settings-saved'));
    }

    /**
     * Edit user general settings.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.general-setting.edit', ['user' => $user]);
    }
}
