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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Override;

class UpdateForumRequest extends FormRequest
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
     * @return array<string, array<\Illuminate\Validation\Rules\In|string>>
     */
    public function rules(): array
    {
        return [
            'forum.name' => [
                'required',
            ],
            'forum.position' => [
                'required',
            ],
            'forum.slug' => [
                'required',
            ],
            'forum.description' => [
                'required',
            ],
            'forum.forum_category_id' => [
                'required',
                'exists:forum_categories,id',
            ],
            'forum.default_topic_state_filter' => [
                'sometimes',
                'nullable',
                Rule::in(['close', 'open', null]),
            ],
            'permissions' => [
                'required',
                'array',
            ],
            'permissions.*' => [
                'required',
                'array:group_id,read_topic,reply_topic,start_topic',
            ],
            'permissions.*.group_id' => [
                'required',
                'exists:groups,id',
            ],
            'permissions.*.read_topic' => [
                'required',
                'boolean',
            ],
            'permissions.*.reply_topic' => [
                'required',
                'boolean',
            ],
            'permissions.*.start_topic' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $data = $this->toArray();

        data_set($data, 'forum.slug', Str::slug($this->input('forum.name')));

        $this->merge($data);
    }
}
