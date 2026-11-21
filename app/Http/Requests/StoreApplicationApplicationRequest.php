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

namespace App\Http\Requests;

use App\Models\Application;
use App\Models\Invite;
use App\Models\User;
use App\Rules\EmailBlacklist;
use Illuminate\Validation\Rule as LaravelRule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreApplicationApplicationRequest extends Data
{
    public function __construct(
        #[In('New to the game', 'Experienced with private trackers')]
        public string $type,
        #[Email, Max(70), Rule([
            new Unique(Invite::class, 'email'),
            new Unique(User::class, 'email'),
            new Unique(Application::class, 'email'),
        ])]
        public string $email,
        public string $referrer,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'email' => [
                new Rule([
                    LaravelRule::when(config('email-blacklist.enabled'), new EmailBlacklist()),
                ])
            ]
        ];
    }
}
