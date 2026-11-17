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

namespace App\Http\Requests\User;

use App\Attributes\FromCurrentUserId;
use App\Models\Post;
use App\Models\User;
use Closure;
use Override;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Different;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\FieldReference;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StorePostTipRequest extends Data
{
    public function __construct(
        public int $postId,
        public int $bon,
        #[FromCurrentUserId]
        public int $senderId,
        #[Exists(User::class, 'id'), Different(new FieldReference('sender_id'))]
        public int $recipientId,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<Closure(string, mixed, Closure(string): never): void|ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'post_id' => [
                function ($_attribute, $value, $fail): void {
                    if (Post::query()->whereKey($value)->authorized(canReadTopic: true)->doesntExist()) {
                        $fail('Post was not found.');
                    }
                },
            ],
            'bon' => [
                new Between(1, (float) auth()->user()->seedbonus),
            ]
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
            'recipient_id.different' => trans('bon.failed-yourself'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['recipient_id'] = Post::query()
            ->whereKey((int) $properties['post_id'])
            ->authorized(canReadTopic: true)
            ->value('user_id');

        return $properties;
    }
}
