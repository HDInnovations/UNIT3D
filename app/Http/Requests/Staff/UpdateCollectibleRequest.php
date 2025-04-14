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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollectibleRequest extends FormRequest
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
     * @return array<string, array<\Illuminate\Validation\Rules\In|string>|string>
     */
    public function rules(): array
    {
        return [
            'collectible.image' => [
                'max:10240',
            ],
            'collectible.name' => [
                'required',
                'string',
                'max:255',
            ],
            'collectible.description' => [
                'required',
                'string',
            ],
            'collectible.category_id' => [
                'required',
                'exists:collectible_categories,id',
            ],
            'collectible.price' => [
                'required',
                'min:0',
                'numeric',
            ],
            'collectible.resell' => [
                'required',
                'boolean',
            ],
            'requirements.*' => [
                'nullable',
                'array:id,operand1,operator,operand2',
                'required_array_keys:operand1,operator,operand2',
            ],
            'requirements.*.operand1' => [
                'required',
                Rule::in([
                    'uploaded',
                    'seedsize',
                    'average_seedtime',
                    'ratio',
                    'age',
                ]),
            ],
            'requirements.*.operator' => [
                'required',
                Rule::in([
                    '<',
                    '>',
                    '<=',
                    '>=',
                    '=',
                    '!=',
                ]),
            ],
            'requirements.*.operand2' => [
                'required',
                'numeric',
            ],
        ];
    }
}
