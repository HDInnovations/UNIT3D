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
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        return $request->user()->group->is_owner || (
            $request->group['level'] < $request->user()->group->level
            && !$request->group['is_owner']
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\ConditionalRules|string>|string>
     */
    public function rules(Request $request): array
    {
        return [
            'group.name' => [
                'required',
                'string',
                'unique:groups,name',
            ],
            'group.position' => [
                'required',
                'integer',
            ],
            'group.level' => [
                'required',
                'integer',
            ],
            'group.download_slots' => [
                'nullable',
                'integer',
            ],
            'group.description' => [
                'nullable',
            ],
            'group.color' => [
                'required',
            ],
            'group.icon' => [
                'required',
            ],
            'group.effect' => [
                'sometimes',
            ],
            'group.is_uploader' => [
                'required',
                'boolean',
            ],
            'group.is_internal' => [
                'required',
                'boolean',
            ],
            'group.is_editor' => [
                'required',
                'boolean',
            ],
            'group.is_torrent_modo' => [
                'required',
                'boolean',
            ],
            'group.is_modo' => [
                'required',
                'boolean',
            ],
            'group.is_admin' => [
                'required',
                'boolean',
            ],
            'group.is_owner' => [
                'required',
                'boolean',
            ],
            'group.is_trusted' => [
                'required',
                'boolean',
            ],
            'group.is_immune' => [
                'required',
                'boolean',
            ],
            'group.is_freeleech' => [
                'required',
                'boolean',
            ],
            'group.is_double_upload' => [
                'required',
                'boolean',
            ],
            'group.is_incognito' => [
                'required',
                'boolean',
            ],
            'group.can_chat' => [
                'required',
                'boolean',
            ],
            'group.can_comment' => [
                'required',
                'boolean',
            ],
            'group.can_invite' => [
                'required',
                'boolean',
            ],
            'group.can_request' => [
                'required',
                'boolean',
            ],
            'group.can_upload' => [
                'required',
                'boolean',
            ],
            'group.autogroup' => [
                'required',
                'boolean',
            ],
            'group.min_uploaded' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'group.min_actual_uploaded' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'group.min_ratio' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'min:0',
                    'max:99.99',
                ], 'prohibited'),
            ],
            'group.min_age' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'group.min_avg_seedtime' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'group.min_seedsize' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'group.min_uploads' => [
                Rule::when($request->boolean('autogroup'), [
                    'sometimes',
                    'integer',
                    'min:0',
                ], 'prohibited'),
            ],
            'permissions' => [
                'required',
                'array',
            ],
            'permissions.*' => [
                'required',
                'array:forum_id,read_topic,reply_topic,start_topic',
            ],
            'permissions.*.forum_id' => [
                'required',
                'exists:forums,id',
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
}
