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

namespace App\Actions\Fortify;

use App\Models\Group;
use App\Models\Invite;
use App\Models\User;
use App\Rules\EmailBlacklist;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string> $input
     * @throws ValidationException
     * @throws Exception
     */
    public function create(array $input): RedirectResponse | User
    {
        Validator::make($input, [
            'username' => 'required|alpha_dash|string|between:3,25|unique:users',
            'password' => [
                'required',
                'confirmed',
                $this->passwordRules(),
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:70',
                'unique:users',
                Rule::when(config('email-blacklist.enabled') === true, fn () => new EmailBlacklist()),
            ],
            'captcha' => [
                Rule::excludeIf(config('captcha.enabled') === false),
                Rule::when(config('captcha.enabled') === true, 'hiddencaptcha'),
            ],
            'code' => [
                Rule::when(config('other.invite-only') === true, [
                    'required',
                    Rule::exists('invites', 'code')->withoutTrashed()->whereNull('accepted_by'),
                ]),
            ]
        ])->validate();

        $user = User::create([
            'username'   => $input['username'],
            'email'      => $input['email'],
            'password'   => Hash::make($input['password']),
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
            $invite = Invite::where('code', '=', $input['code'])->first();
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

        return $user;
    }
}
