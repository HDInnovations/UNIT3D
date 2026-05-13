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

class UpdateRssRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:3',
                'max:255',
            ],
            'search' => [
                'max:255',
            ],
            'description' => [
                'max:255',
            ],
            'uploader' => [
                'max:255',
            ],
            'excluded_uploader' => [
                'max:255',
            ],
            'categories' => [
                'sometimes',
                'array',
                'max:999',
            ],
            'categories.*' => [
                'sometimes',
                'exists:categories,id',
            ],
            'types' => [
                'sometimes',
                'array',
                'max:999',
            ],
            'types.*' => [
                'sometimes',
                'exists:types,id',
            ],
            'resolutions' => [
                'sometimes',
                'array',
                'max:999',
            ],
            'resolutions.*' => [
                'sometimes',
                'exists:resolutions,id',
            ],
            'genres' => [
                'sometimes',
                'array',
                'max:999',
            ],
            'genres.*' => [
                'sometimes',
                'exists:genres,id',
            ],
            'position' => [
                'sometimes',
                'integer',
                'max:9999',
            ],
            'imdb' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'tvdb' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'tmdb' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'mal' => [
                'sometimes',
                'nullable',
                'integer',
            ],
            'freeleech' => [
                'sometimes',
                'boolean',
            ],
            'doubleupload' => [
                'sometimes',
                'boolean',
            ],
            'featured' => [
                'sometimes',
                'boolean',
            ],
            'highspeed' => [
                'sometimes',
                'boolean',
            ],
            'internal' => [
                'sometimes',
                'boolean',
            ],
            'personalrelease' => [
                'sometimes',
                'boolean',
            ],
            'bookmark' => [
                'sometimes',
                'boolean',
            ],
            'alive' => [
                'sometimes',
                'boolean',
            ],
            'dying' => [
                'sometimes',
                'boolean',
            ],
            'dead' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
