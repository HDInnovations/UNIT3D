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

namespace App\Http\Requests\Auth;

use App\Rules\EmailBlacklist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreRegisteredUserRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\ExcludeIf|Password|string>|\Illuminate\Validation\ConditionalRules|string>
     */
    public function rules(): array
    {
        return  [
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
            'email'    => [
                'required',
                'string',
                'email:rfc,dns',
                'max:70',
                'unique:users',
                Rule::when(config('email-blacklist.enabled') === true, fn () => new EmailBlacklist()),
            ],
        ];
    }
}
