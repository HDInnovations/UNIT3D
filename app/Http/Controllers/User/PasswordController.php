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
use App\Notifications\PasswordUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update user password.
     */
    protected function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        $changedByStaff = $request->user()->isNot($user);

        abort_if($changedByStaff && !$request->user()->group->is_owner && $request->user()->group->level <= $user->group->level, 403);

        $request->validate([
            'current_password' => Rule::when(!$changedByStaff, [
                'required',
                'current_password',
            ]),
            'new_password' => [
                'required',
                'confirmed',
                Password::min(12)->mixedCase()->letters()->numbers()->uncompromised(),
            ],
        ]);

        DB::transaction(function () use ($user, $request, $changedByStaff): void {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $user->passwordResetHistories()->create();

            if ($changedByStaff) {
                $user->notify(new PasswordUpdate());
            }
        });

        return to_route('users.password.edit', ['user' => $user])
            ->with('success', __('application-messages.flash.password-saved'));
    }

    /**
     * Edit user password.
     */
    public function edit(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.password.edit', [
            'user'                   => $user,
            'passwordResetHistories' => $user->passwordResetHistories()->latest()->get(),
        ]);
    }
}
