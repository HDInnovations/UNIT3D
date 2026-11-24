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

use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreSubscriptionRequest extends Data
{
    public function __construct(
        public ?int $forumId,
        public ?int $topicId,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Contracts\Validation\Rule|string>>
     */
    public static function rules(): array
    {
        return [
            'forum_id' => [
                'sometimes',
                'prohibits:topic_id',
                'required_without:topic_id',
                'exists:forums,id',
            ],
            'topic_id' => [
                'sometimes',
                'prohibits:forum_id',
                'required_without:forum_id',
                'exists:topics,id',
            ],
        ];
    }
}
