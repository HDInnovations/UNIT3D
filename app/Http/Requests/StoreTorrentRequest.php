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
use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use App\Services\Media\MediaWorkCatalog;
use App\Services\Metadata\MetadataSelectionStore;
use Closure;
use Exception;

class StoreTorrentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user?->can_upload ?? $user?->group?->can_upload ?? false);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $category = Category::find($this->integer('category_id'));
        $kind = $category ? UploadKinds::categoryKind($category) : 'no';
        $fieldsByKind = [
            'tmdb_movie_id' => ['movie'], 'movie_exists_on_tmdb' => ['movie'],
            'tmdb_tv_id' => ['tv'], 'tv_exists_on_tmdb' => ['tv'],
            'imdb' => ['movie', 'tv'], 'title_exists_on_imdb' => ['movie', 'tv'],
            'tvdb' => ['tv'], 'tv_exists_on_tvdb' => ['tv'],
            'mal' => ['movie', 'tv'], 'anime_exists_on_mal' => ['movie', 'tv'],
            'igdb' => ['game'], 'game_exists_on_igdb' => ['game'],
            'musicbrainz_id' => ['music'], 'open_library_edition_id' => ['book'],
            'season_number' => ['tv'], 'episode_number' => ['tv'],
            'resolution_id' => ['movie', 'tv', 'xxx'],
            'region_id' => ['movie', 'tv', 'xxx'], 'distributor_id' => ['movie', 'tv', 'xxx'],
            'mediainfo' => ['movie', 'tv', 'music', 'xxx'], 'bdinfo' => ['movie', 'tv', 'xxx'],
            'edition_kind' => ['movie', 'tv'], 'edition_name' => ['movie', 'tv'],
            'edition_provenance' => ['movie', 'tv'],
        ];
        foreach ($fieldsByKind as $field => $kinds) {
            if (!in_array($kind, $kinds, true)) {
                $this->request->remove($field);
            }
        }

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

        // Look the category up without aborting: an invalid/missing category_id
        // must surface as a normal validation error on the `category_id` field
        // (via the `exists:categories,id` rule below), not a 404. When the
        // category can't be found, fall back to a blank in-memory category so
        // every metadata rule below degrades to its "no metadata" branch.
        $category = Category::find($request->integer('category_id')) ?? new Category([
            'name'       => '',
            'movie_meta' => false,
            'tv_meta'    => false,
            'game_meta'  => false,
            'music_meta' => false,
            'book_meta'  => false,
            'no_meta'    => true,
        ]);

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'metadata_selection_token' => ['nullable', 'uuid'],
            'media_work_id' => ['nullable', 'integer', 'exists:media_works,id'],
            'upload_draft_id' => [
                'nullable',
                'integer',
                Rule::exists('upload_drafts', 'id')->where('user_id', $user->id),
            ],
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
                'max:255',
            ],
            'description' => [
                'required',
                'max:65535'
            ],
            'mediainfo' => [
                Rule::when(
                    \in_array(UploadKinds::categoryKind($category), ['movie', 'tv', 'music', 'xxx'], true),
                    ['nullable', 'sometimes', 'max:65535'],
                    [$mustBeNull],
                ),
            ],
            'bdinfo' => [
                Rule::when(
                    \in_array(UploadKinds::categoryKind($category), ['movie', 'tv', 'xxx'], true),
                    ['nullable', 'sometimes', 'max:2097152'],
                    [$mustBeNull],
                ),
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'type_id' => [
                'required',
                'exists:types,id',
                function (string $attribute, mixed $value, Closure $fail) use ($category): void {
                    $type = \App\Models\Type::find($value);

                    if ($type !== null && !UploadKinds::typeAppliesToKind($type->name, UploadKinds::categoryKind($category))) {
                        $fail('The selected type is not applicable to this category.');
                    }
                },
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
            'musicbrainz_id' => [
                Rule::when($category->music_meta, [
                    'nullable',
                    'uuid',
                ]),
                Rule::when(!$category->music_meta, [
                    $mustBeNull,
                ]),
            ],
            'open_library_edition_id' => [
                Rule::when($category->book_meta, [
                    'nullable',
                    'string',
                    'max:32',
                    'regex:/^(?:OL\d+M|[0-9X-]{10,17})$/i',
                ]),
                Rule::when(!$category->book_meta, [
                    $mustBeNull,
                ]),
            ],
            'edition_kind' => [
                Rule::when($category->movie_meta || $category->tv_meta, ['nullable', Rule::in(['standard', 'director_cut', 'extended_cut', 'restored', 'fan_edit'])]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [$mustBeNull]),
            ],
            'edition_name' => [
                Rule::when($category->movie_meta || $category->tv_meta, ['nullable', 'string', 'max:255']),
                Rule::when(!($category->movie_meta || $category->tv_meta), [$mustBeNull]),
            ],
            'edition_provenance' => [
                Rule::when($category->movie_meta || $category->tv_meta, ['nullable', 'string', 'max:2000']),
                Rule::when(!($category->movie_meta || $category->tv_meta), [$mustBeNull]),
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
            'mod_queue_opt_in' => [
                'sometimes',
                'boolean',
                Rule::excludeIf(! $user->group->is_trusted),
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

    /** @return array<Closure(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $category = Category::findOrFail($this->integer('category_id'));
            $lookup = null;

            try {
                if ($this->filled('metadata_selection_token')) {
                    $lookup = app(MetadataSelectionStore::class)->retrieve(
                        $this->user(), $category, $this->string('metadata_selection_token')->toString(),
                    );

                    foreach (['tmdb_movie_id', 'tmdb_tv_id', 'igdb', 'musicbrainz_id', 'open_library_edition_id'] as $field) {
                        if (!\array_key_exists($field, $lookup['identifiers'] ?? [])) {
                            continue;
                        }

                        $expected = $this->normalizedIdentifier($field, (string) $lookup['identifiers'][$field]);
                        $actual = $this->normalizedIdentifier($field, (string) $this->input($field, ''));

                        if ($expected !== $actual) {
                            throw ValidationException::withMessages([
                                'metadata_selection_token' => __('metadata.errors.selection-mismatch'),
                            ]);
                        }
                    }
                }

                if ($this->filled('media_work_id')) {
                    $prospective = new Torrent();
                    foreach (['category_id', 'tmdb_movie_id', 'tmdb_tv_id', 'igdb', 'musicbrainz_id', 'open_library_edition_id'] as $field) {
                        $prospective->setAttribute($field, $this->input($field));
                    }
                    $prospective->user_id = $this->user()->id;

                    app(MediaWorkCatalog::class)->validateSelection($prospective, $lookup, $this->integer('media_work_id'));
                }
            } catch (ValidationException $exception) {
                foreach ($exception->errors() as $field => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($field, $message);
                    }
                }
            }
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'upload_draft_id.exists' => __('metadata.errors.draft-unavailable'),
            'media_work_id.exists' => __('metadata.errors.work-mismatch'),
        ];
    }

    private function normalizedIdentifier(string $field, string $identifier): string
    {
        if (\in_array($field, ['tmdb_movie_id', 'tmdb_tv_id', 'igdb'], true)) {
            return $identifier === '' ? '' : (string) (int) $identifier;
        }

        return strtolower(str_replace([' ', '-'], '', trim($identifier)));
    }
}
