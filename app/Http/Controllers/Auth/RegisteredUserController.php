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
use App\Models\Invite;
use App\Models\User;
use App\Rules\EmailBlacklist;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        if ($request->missing('code')) {
            return view('auth.register');
        }

        return view('auth.register', ['code' => $request->query('code')]);
    }

    /**
     * Receive registration form.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Validator::make($request->all(), [
            'code' => [
                Rule::when(config('other.invite-only') === true, [
                    'required',
                    Rule::exists('invites', 'code')->withoutTrashed()->whereNull('accepted_by'),
                ]),
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)->mixedCase()->letters()->numbers()->uncompromised(),
            ],
            'captcha' => [
                Rule::excludeIf(config('captcha.enabled') === false),
                Rule::when(config('captcha.enabled') === true, 'hiddencaptcha'),
            ],
            'username' => 'required|alpha_dash|string|between:3,25|unique:users',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:70',
                'unique:users',
                Rule::when(config('email-blacklist.enabled') === true, fn () => new EmailBlacklist()),
            ],
        ])->stopOnFirstFailure()->validate();

        $user = User::create([
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'passkey'    => md5(random_bytes(60)),
            'rsskey'     => md5(random_bytes(60)),
            'uploaded'   => config('other.default_upload'),
            'downloaded' => config('other.default_download'),
            'group_id'   => Group::query()->where('slug', '=', 'validating')->soleValue('id'),
        ]);

        $user->passkeys()->create(['content' => $user->passkey]);

        $user->rsskeys()->create(['content' => $user->rsskey]);

        $user->emailUpdates()->create();

        if (config('other.invite-only') === true) {
            $invite = Invite::where('code', '=', $request->code)->first();
            $invite->update([
                'accepted_by' => $user->id,
                'accepted_at' => now(),
            ]);

            if ($invite->internal_note !== null) {
                $user->notes()->create([
                    'message'  => $invite->internal_note,
                    'staff_id' => $invite->user_id,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return to_route('verification.notice');
    }
}
