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

use App\Enums\ModerationStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MergeValidationRules]
class UpdatePlaylistSuggestionRequest extends Data
{
    public function __construct(
        #[Enum(ModerationStatus::class, only: [ModerationStatus::APPROVED, ModerationStatus::REJECTED])]
        public ModerationStatus $status,
        public ?string $rejectionMessage,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Validation\Rules\Enum|\Illuminate\Validation\Rules\RequiredIf|string>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'rejection_message' => [
                Rule::requiredIf((int) $context->payload['status'] === ModerationStatus::REJECTED->value),
                'max:65535',
            ],
        ];
    }
}
