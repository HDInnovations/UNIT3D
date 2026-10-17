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

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Closure;

class UpdateAutomaticTorrentFreeleechRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, (Closure)|string>>.
     */
    public function rules(): array
    {
        return [
            'name_regex' => [
                'nullable',
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if (@preg_match($value, 'Validate regex') === false) {
                        $fail('Regex syntax error.');
                    }
                },
            ],
            'position'             => ['required', 'integer', 'min:0'],
            'size'                 => ['nullable', 'integer', 'min:0'],
            'category_id'          => ['nullable', 'integer', 'exists:categories,id'],
            'type_id'              => ['nullable', 'integer', 'exists:types,id'],
            'resolution_id'        => ['nullable', 'integer', 'exists:resolutions,id'],
            'freeleech_percentage' => ['required', 'integer', 'max:100', 'min:0'],
        ];
    }
}
