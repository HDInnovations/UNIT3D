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

namespace App\Http\Requests\API;

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
use Override;

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
    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tmdb_movie_id'    => $this->input('tmdb') ?: null,
            'tmdb_tv_id'       => $this->input('tmdb') ?: null,
            'imdb'             => $this->input('imdb') ?: null,
            'tvdb'             => $this->input('tvdb') ?: null,
            'mal'              => $this->input('mal') ?: null,
            'igdb'             => $this->input('igdb') ?: null,
            'anon'             => $this->input('anonymous'),
            'personal_release' => $this->input('personal_release') ?? false,
            'du_until'         => $this->integer('du_until') ? now()->addDays($this->integer('du_until')) : null,
            'fl_until'         => $this->integer('fl_until') ? now()->addDays($this->integer('fl_until')) : null,
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
        $category = Category::query()->findOrFail($request->integer('category_id'));

        return [
            'torrent' => [
                'required',
                'file',
                function (string $_attribute, mixed $value, Closure $fail): void {
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

                    $torrent = Torrent::query()->withoutGlobalScope(ApprovedScope::class)->where('info_hash', '=', Bencode::get_infohash($decodedTorrent))->first();

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
                function (string $_attribute, mixed $value, Closure $fail): void {
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
            'description' => [
                'required',
                'max:65535'
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
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!($category->movie_meta || $category->tv_meta)),
            ],
            'tvdb' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!$category->tv_meta),
            ],
            'tmdb_movie_id' => [
                Rule::when($category->movie_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!$category->movie_meta),
            ],
            'tmdb_tv_id' => [
                Rule::when($category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!$category->tv_meta),
            ],
            'mal' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!($category->movie_meta || $category->tv_meta)),
            ],
            'igdb' => [
                Rule::when($category->game_meta, [
                    'nullable',
                    'decimal:0',
                    'min:0',
                ]),
                Rule::excludeIf(!$category->game_meta),
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
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'sticky' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'featured' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'free' => [
                'sometimes',
                'integer',
                'numeric',
                'between:0,100',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'doubleup' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'refundable' => [
                'sometimes',
                'boolean',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'du_until' => [
                'nullable',
                'date',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'fl_until' => [
                'nullable',
                'date',
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
        ];
    }
}
