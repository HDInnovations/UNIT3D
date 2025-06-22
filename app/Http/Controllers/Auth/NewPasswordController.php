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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    /**
     * Create new password.
     */
    public function create(string $token): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Store a new password.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => RulesPassword::min(12)->mixedCase()->letters()->numbers()->uncompromised(),
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $validatingGroup = cache()->rememberForever('validating_group', fn () => Group::query()->where('slug', '=', 'validating')->pluck('id'));
                $memberGroup = cache()->rememberForever('member_group', fn () => Group::query()->where('slug', '=', 'user')->pluck('id'));

                if ($user->group_id === $validatingGroup[0]) {
                    $user->group_id = $memberGroup[0];

                    cache()->forget('user:'.$user->passkey);

                    Unit3dAnnounce::addUser($user);
                }

                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }

                $user->save();

                $user->passwordResetHistories()->create();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
