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

use App\Models\Poll;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Override;

/**
 * @see \Tests\Todo\Unit\Http\Requests\VoteOnPollTest
 */
class StorePollVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\Exists|string>>
     */
    public function rules(Request $request): array
    {
        /** @var Poll $poll */
        $poll = $request->route('poll');

        return [
            'options' => [
                'required',
                'array',
                Rule::when(!$poll->multiple_choice, 'max:1'),
                'min:1',
            ],
            'options.*' => [
                'integer',
                Rule::exists('options', 'id')->where('poll_id', $poll->id),
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    #[Override]
    public function messages(): array
    {
        return [
            'options.required' => 'You must select an answer',
        ];
    }
}
