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

use App\Enums\UserGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertUserNotificationRequest;
use App\Models\Group;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    /**
     * Update user notification settings.
     */
    public function update(UpsertUserNotificationRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        // Can't use upsert here because upsert doesn't serialize the custom
        // array cast to a string before upserting
        UserNotification::query()->updateOrCreate(['user_id' => $user->id], $request->validated());

        cache()->forget('user-notification:by-user-id:'.$user->id);

        return to_route('users.notification_settings.edit', ['user' => $user])
            ->with('success', 'Your notification settings have been successfully saved.');
    }

    /**
     * Edit user notification settings.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.notification-setting.edit', [
            'user'   => $user,
            'groups' => Group::query()
                ->where('is_modo', '=', false)
                ->where('is_admin', '=', false)
                ->where('id', '!=', UserGroup::VALIDATING->value)
                ->where('id', '!=', UserGroup::PRUNED->value)
                ->where('id', '!=', UserGroup::BANNED->value)
                ->where('id', '!=', UserGroup::DISABLED->value)
                ->latest('level')
                ->get(),
        ]);
    }
}
