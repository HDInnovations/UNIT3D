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

use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Override;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Prohibited;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Container\Attributes\RouteParameter;

#[MergeValidationRules]
class UpdateGroupGroupRequest extends Data
{
    public function __construct(
        public ?string $name,
        public string $slug,
        public int $position,
        public int $level,
        public ?int $downloadSlots,
        public ?string $description,
        public string $color,
        public string $icon,
        public ?string $effect,
        public bool $isUploader,
        public bool $isInternal,
        public bool $isEditor,
        public bool $isTorrentModo,
        public bool $isModo,
        public bool $isAdmin,
        public bool $isOwner,
        public bool $isTrusted,
        public bool $isImmune,
        public bool $isFreeleech,
        public bool $isDoubleUpload,
        public bool $isIncognito,
        public bool $canChat,
        public bool $canComment,
        public bool $canInvite,
        public bool $canRequest,
        public bool $canUpload,
        public bool $autogroup,
        public ?int $minUploaded,
        public ?int $minActualUploaded,
        public ?int $minRatio,
        public ?int $minAge,
        public ?int $minAvgSeedtime,
        public ?int $minSeedsize,
        public ?int $minAvgSeedsize,
        public ?int $minUploads,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['slug'] = Str::slug($properties['name'] ?? '');

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\ConditionalRules|string>|string>
     */
    public static function rules(#[RouteParameter('group')] Group $group, ValidationContext $context): array
    {
        return [
            'name' => [
                Rule::when(! $group->system_required, [
                    new Required()
                ]),
                Rule::when($group->system_required && $context->payload['name'] !== $group->name, [
                    new Prohibited(),
                ]),
            ],
            'min_uploaded' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_actual_uploaded' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_ratio' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'min:0',
                    'max:99.99',
                ], 'nullable'),
            ],
            'min_age' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_avg_seedtime' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_seedsize' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_avg_seedsize' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
            'min_uploads' => [
                Rule::when((bool) $context->payload['autogroup'], [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'nullable'),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'name.prohibited' => 'You cannot change the name of a system required group.',
        ];
    }
}
