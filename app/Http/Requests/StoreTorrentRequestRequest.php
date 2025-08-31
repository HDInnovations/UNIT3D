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

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreTorrentRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tmdb_movie_id' => $this->has('movie_exists_on_tmdb') ? ($this->input('tmdb_movie_id') ?: null) : null,
            'tmdb_tv_id'    => $this->has('tv_exists_on_tmdb') ? ($this->input('tmdb_tv_id') ?: null) : null,
            'imdb'          => $this->has('title_exists_on_imdb') ? ($this->input('imdb') ?: null) : null,
            'tvdb'          => $this->has('tv_exists_on_tvdb') ? ($this->input('tvdb') ?: null) : null,
            'mal'           => $this->has('anime_exists_on_mal') ? ($this->input('mal') ?: null) : null,
            'igdb'          => $this->has('game_exists_on_igdb') ? ($this->input('igdb') ?: null) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\ConditionalRules|string>|string>
     */
    public function rules(Request $request): array
    {
        $category = Category::findOrFail($request->integer('category_id'));

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'name' => [
                'required',
                'max:180',
            ],
            'imdb' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:title_exists_on_imdb',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'tvdb' => [
                Rule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tvdb',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'season_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                    'integer',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                    'integer',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_movie_id' => [
                Rule::when($category->movie_meta, [
                    'required_with:movie_exists_on_tmdb',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->movie_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_tv_id' => [
                Rule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tmdb',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'mal' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:anime_exists_on_mal',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'igdb' => [
                Rule::when($category->game_meta, [
                    'required_with:game_exists_on_igdb',
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::when(!$category->game_meta, [
                    $mustBeNull,
                ]),
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'type_id' => [
                'nullable',
                'exists:types,id',
            ],
            'resolution_id' => [
                'nullable',
                'exists:resolutions,id',
            ],
            'description' => [
                'required',
                'string',
            ],
            'bounty' => [
                'required',
                'numeric',
                'min:100',
                'max:'.$request->user()->seedbonus,
            ],
            'anon' => [
                'boolean',
                'required',
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
            'bounty.max' => 'You do not have enough BON to make this request.',
        ];
    }
}
