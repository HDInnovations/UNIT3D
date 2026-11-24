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

use App\Models\User;
use App\Rules\EmailBlacklist;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\AlphaDash;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreRegisteredUserRequest extends Data
{
    public function __construct(
        public ?string $code,
        #[Confirmed, Password(min: 12, letters: true, mixedCase: true, numbers: true, uncompromised: true)]
        public string $password,
        public ?string $captcha,
        #[AlphaDash, Between(3, 25), Unique(User::class)]
        public string $username,
        #[Email(Email::RfcValidation, Email::DnsCheckValidation), Max(70), Unique(User::class)]
        public string $email,
    ) {
    }

    public static function stopOnFirstFailure(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\ExcludeIf>>
     */
    public static function rules(): array
    {
        return [
            'code' => [
                Rule::when(config('other.invite-only') === true, [
                    'required',
                    Rule::exists('invites', 'code')->withoutTrashed()->whereNull('accepted_by'),
                ])
            ],
            'captcha' => [
                Rule::excludeIf(config('captcha.enabled') === false),
                Rule::when(config('captcha.enabled') === true, 'hiddencaptcha'),
            ],
            'email' => [
                Rule::when(config('email-blacklist.enabled') === true, fn () => new EmailBlacklist()),
            ],
        ];
    }
}
