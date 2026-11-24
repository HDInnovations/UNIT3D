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

namespace App\Http\Requests;

use App\Models\Option;
use App\Models\Poll;
use Illuminate\Validation\Rule;
use Illuminate\Container\Attributes\RouteParameter;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\ValidationAttribute;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\Constraints\WhereConstraint;

/**
 * @see \Tests\Todo\Unit\Http\Requests\VoteOnPollTest
 */
#[MergeValidationRules]
class StorePollVoteRequest extends Data
{
    public function __construct(
        /** @var list<int> */
        public array $options,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Validation\ConditionalRules|ValidationAttribute|string>>
     */
    public static function rules(#[RouteParameter('poll')] Poll $poll): array
    {
        return [
            'options' => [
                new Min(1),
                Rule::when(!$poll->multiple_choice, [
                    new Max(1)
                ]),
            ],
            'options.*' => [
                'integer',
                new Exists(Option::class, 'id', where: new WhereConstraint('poll_id', $poll->id)),
            ]
        ];
    }
}
