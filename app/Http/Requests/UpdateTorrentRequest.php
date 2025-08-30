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
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateTorrentRequest extends FormRequest
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
     * @return array<string, list<\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        $category = Category::findOrFail($request->integer('category_id'));

        /** @var string $torrentId */
        $torrentId = $request->route('id');

        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->find($torrentId);
        $user = $request->user()->load('group')->loadExists('internals');

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'name' => [
                'required',
                Rule::unique('torrents')->whereNot('id', $torrentId)->whereNull('deleted_at'),
                'max:255',
            ],
            'description' => [
                'required',
                'max:2097152'
            ],
            'mediainfo' => [
                'nullable',
                'sometimes',
                'max:2097152',
            ],
            'bdinfo' => [
                'nullable',
                'sometimes',
                'max:2097152',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'type_id' => [
                'required',
                'exists:types,id',
            ],
            'resolution_id' => [
                Rule::when($category->movie_meta || $category->tv_meta, 'required'),
                Rule::when(!$category->movie_meta && !$category->tv_meta, 'nullable'),
                'exists:resolutions,id',
            ],
            'region_id' => [
                'nullable',
                'exists:regions,id',
            ],
            'distributor_id' => [
                'nullable',
                'exists:distributors,id',
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
            'season_number' => [
                Rule::when($category->tv_meta, 'required'),
                Rule::when(!$category->tv_meta, 'nullable'),
                'decimal:0',
                'min:0',
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, 'required'),
                Rule::when(!$category->tv_meta, 'nullable'),
                'decimal:0',
                'min:0',
            ],
            'anon' => [
                'sometimes',
                'boolean',
                Rule::requiredIf($torrent->user_id === $user->id || $user->group->is_modo),
                Rule::excludeIf(!($torrent->user_id === $user->id || $user->group->is_modo)),
            ],
            'personal_release' => [
                'sometimes',
                'boolean',
                Rule::requiredIf($torrent->user_id === $user->id || $user->group->is_modo),
                Rule::excludeIf(!($torrent->user_id === $user->id || $user->group->is_modo)),
            ],
            'internal' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'free' => [
                'sometimes',
                'integer',
                'numeric',
                'between:0,100',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'refundable' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::requiredIf($user->group->is_modo || $user->internals_exists),
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
                    // Porn category ID fields
                    'fansdb_id' => [
                        Rule::when($category->porn_meta, [
                            'nullable',
                            'string',
                            'max:255',
                        ]),
                        Rule::when(!$category->porn_meta, [
                            $mustBeNull,
                        ]),
                    ],
                    'stashdb_id' => [
                        Rule::when($category->porn_meta, [
                            'nullable',
                            'string',
                            'max:255',
                        ]),
                        Rule::when(!$category->porn_meta, [
                            $mustBeNull,
                        ]),
                    ],
                    'theporndb_id' => [
                        Rule::when($category->porn_meta, [
                            'nullable',
                            'string',
                            'max:255',
                        ]),
                        Rule::when(!$category->porn_meta, [
                            $mustBeNull,
                        ]),
                    ],
        ];
    }
}
