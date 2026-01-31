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

use App\Models\Post;
use App\Models\Topic;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StorePostTipRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure|string>|string>
     */
    public function rules(Request $request): array
    {
        $user = $request->user();

        return [
            'post_id' => [
                'required',
                function ($attribute, $value, $fail): void {
                    if (
                        Post::query()->whereKey($value)->whereNotIn(
                            'topic_id',
                            Topic::query()
                                ->whereRelation(
                                    'forumPermissions',
                                    fn ($query) => $query
                                        ->where('group_id', '=', auth()->user()->group_id)
                                        ->where('read_topic', '!=', 1)
                                )
                                ->select('id'),
                        )
                            ->doesntExist()
                    ) {
                        $fail('Post was not found.');
                    }
                },
            ],
            'bon' => [
                'required',
                'numeric',
                'min:1',
                'max:'.$user->seedbonus,
            ],
            'sender_id' => [
                'required',
                'exists:users,id',
            ],
            'recipient_id' => [
                'required',
                'exists:users,id',
                'different:sender_id',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bon.min'                => trans('bon.failed-negative'),
            'bon.max'                => trans('bon.failed-funds-poster'),
            'recipient_id.different' => trans('bon.failed-yourself'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender_id'    => auth()->id(),
            'recipient_id' => Post::query()
                ->whereKey($this->post_id)
                ->whereNotIn(
                    'topic_id',
                    Topic::query()
                        ->whereRelation(
                            'forumPermissions',
                            fn ($query) => $query
                                ->where('group_id', '=', auth()->user()->group_id)
                                ->where('read_topic', '!=', 1)
                        )
                        ->select('id'),
                )->value('user_id'),
        ]);
    }
}
