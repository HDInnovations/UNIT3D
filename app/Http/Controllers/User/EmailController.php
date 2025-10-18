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
use App\Models\User;
use App\Notifications\UserEmailChange;
use App\Rules\EmailBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    /**
     * Update user email.
     */
    protected function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        $changedByStaff = $request->user()->isNot($user);

        abort_if($changedByStaff && !$request->user()->group->is_owner && $request->user()->group->level <= $user->group->level, 403);

        $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:70',
                'unique:users',
                Rule::when(config('email-blacklist.enabled'), fn () => new EmailBlacklist()),
            ],
        ]);

        $oldEmail = $user->email;
        $newEmail = $request->email;

        $user->notify(new UserEmailChange($user, $oldEmail, $newEmail));

        DB::transaction(function () use ($user, $newEmail): void {
            $user->emailUpdates()->latest()->first()?->update(['deleted_at' => now()]);

            $user->update([
                'email'             => $newEmail,
                'email_verified_at' => null,
            ]);

            $user->emailUpdates()->create();
        });

        $user->sendEmailVerificationNotification();

        return to_route('users.email.edit', ['user' => $user])
            ->with('success', 'Your email was updated successfully.');
    }

    /**
     * Edit user email.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.email.edit', [
            'user'         => $user,
            'emailUpdates' => $user->emailUpdates()->latest()->get(),
        ]);
    }
}
