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

use App\Enums\ModerationStatus;
use App\Helpers\Bencode;
use App\Helpers\TorrentTools;
use App\Models\Category;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Closure;
use Exception;

class StoreTorrentRequest extends FormRequest
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
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\ProhibitedIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        $user = $request->user()->loadExists('internals');
        $category = Category::findOrFail($request->integer('category_id'));

        $exclusiveImageSource = function (string $attribute, mixed $value, callable $fail) use ($request): void {
            $field = str_replace('torrent-', '', $attribute);

            if ($request->filled("{$field}_url") && $request->hasFile($attribute)) {
                $fail("Only (1) {$field} may be submitted, either by URL or file.");
            }
        };

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'torrent' => [
                'required',
                'file',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value->getClientOriginalExtension() !== 'torrent') {
                        $fail('The torrent file uploaded does not have a ".torrent" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }

                    $decodedTorrent = TorrentTools::normalizeTorrent($value);

                    $v2 = Bencode::is_v2_or_hybrid($decodedTorrent);

                    if ($v2) {
                        $fail('BitTorrent v2 (BEP 52) is not supported!');
                    }

                    try {
                        $meta = Bencode::get_meta($decodedTorrent);
                    } catch (Exception) {
                        $fail('You Must Provide A Valid Torrent File For Upload!');
                    }

                    foreach (TorrentTools::getFilenameArray($decodedTorrent) as $name) {
                        if (!TorrentTools::isValidFilename($name)) {
                            $fail('Invalid Filenames In Torrent Files!');
                        }
                    }

                    $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->where('info_hash', '=', Bencode::get_infohash($decodedTorrent))->first();

                    if ($torrent !== null) {
                        match ($torrent->status) {
                            ModerationStatus::PENDING   => $fail('A torrent with the same info_hash has already been uploaded and is pending moderation.'),
                            ModerationStatus::APPROVED  => $fail('A torrent with the same info_hash has already been uploaded and has been approved.'),
                            ModerationStatus::REJECTED  => $fail('A torrent with the same info_hash has already been uploaded and has been rejected.'),
                            ModerationStatus::POSTPONED => $fail('A torrent with the same info_hash has already been uploaded and is currently postponed.'),
                        };
                    }
                }
            ],
            'nfo' => [
                'nullable',
                'sometimes',
                'file',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value->getClientOriginalExtension() !== 'nfo') {
                        $fail('The NFO uploaded does not have a ".nfo" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }
                },
            ],
            'name' => [
                'required',
                Rule::unique('torrents')->whereNull('deleted_at'),
                'max:255',
            ],
            'cover_url' => [
                Rule::when($category->music_meta || $category->no_meta, 'nullable', 'url'),
                Rule::when(!($category->music_meta || $category->no_meta), [$mustBeNull])
            ],
            'banner_url' => [
                Rule::when($category->music_meta || $category->no_meta, 'nullable', 'url'),
                Rule::when(!($category->music_meta || $category->no_meta), [$mustBeNull])
            ],
            'torrent-cover' => [
                Rule::when($category->music_meta || $category->no_meta, [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,webp',
                    'max:10240',
                    $exclusiveImageSource
                ]),
                Rule::when(!($category->music_meta || $category->no_meta), [$mustBeNull]),
            ],
            'torrent-banner' => [
                Rule::when($category->music_meta || $category->no_meta, [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,webp',
                    'max:10240',
                    $exclusiveImageSource
                ]),
                Rule::when(!($category->music_meta || $category->no_meta), [$mustBeNull]),
            ],
            'description' => [
                'required',
                'max:65535'
            ],
            'mediainfo' => [
                'nullable',
                'sometimes',
                'max:65535',
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
                Rule::when($category->tv_meta, [
                    'required',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::prohibitedIf(!$category->tv_meta),
            ],
            'anon' => [
                'required',
                'boolean',
            ],
            'personal_release' => [
                'required',
                'boolean',
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
        ];
    }
}
