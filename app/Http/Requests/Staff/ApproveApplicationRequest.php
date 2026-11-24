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

namespace App\Http\Requests\Staff;

use App\Attributes\FromCurrentUserId;
use App\Enums\ModerationStatus;
use App\Models\Invite;
use App\Models\User;
use App\Rules\EmailBlacklist;
use Illuminate\Validation\Rule as LaravelRule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class ApproveApplicationRequest extends Data
{
    public function __construct(
        #[Enum(ModerationStatus::class, only: [ModerationStatus::APPROVED])]
        public ModerationStatus $status,
        #[Email, Max(70), Rule([
            new Unique(Invite::class),
            new Unique(User::class),
        ])]
        public string $email,
        public string $approve,
        #[FromCurrentUserId]
        public int $moderatedBy,
    ) {
    }

    /**
     * @return array<string, list<\Illuminate\Validation\ConditionalRules>>
     */
    public static function rules(): array
    {
        return [
            'email' => [
                LaravelRule::when(config('email-blacklist.enabled'), fn () => new EmailBlacklist())
            ],
        ];
    }
}
