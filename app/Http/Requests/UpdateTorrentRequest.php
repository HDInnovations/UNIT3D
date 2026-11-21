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

use App\Helpers\TorrentTools;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Override;
use Illuminate\Container\Attributes\RouteParameter;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\Constraints\WhereNotConstraint;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

#[MergeValidationRules]
class UpdateTorrentRequest extends Data
{
    public function __construct(
        #[Max(255), Unique(Torrent::class, where: new WhereNotConstraint('id', new RouteParameterReference('id')))]
        public string $name,
        #[Max(2 * 1024 * 1024)]
        public string $description,
        #[Exists(Category::class, 'id')]
        public int $categoryId,
        #[Exists(Type::class, 'id')]
        public int $typeId,
        #[Max(2 * 1024 * 1024)]
        public ?string $mediainfo = null,
        #[Max(2 * 1024 * 1024)]
        public ?string $bdinfo = null,
        #[Exists(Resolution::class, 'id')]
        public ?int $resolutionId = null,
        #[Exists(Region::class, 'id')]
        public ?int $regionId = null,
        #[Exists(Distributor::class, 'id')]
        public ?int $distributorId = null,
        #[MapInputName('torrent-cover'), File, Image, Max(2 * 1024 * 1024), Hidden]
        public ?UploadedFile $poster = null,
        #[MapInputName('torrent-banner'), File, Image, Max(2 * 1024 * 1024), Hidden]
        public ?UploadedFile $backdrop = null,
        #[Min(0)]
        public ?int $imdb = null,
        #[Min(0)]
        public ?int $tvdb = null,
        #[Min(0)]
        public ?int $tmdbMovieId = null,
        #[Min(0)]
        public ?int $tmdbTvId = null,
        #[Min(0)]
        public ?int $mal = null,
        #[Min(0)]
        public ?int $igdb = null,
        #[Min(0)]
        public ?int $seasonNumber = null,
        #[Min(0)]
        public ?int $episodeNumber = null,
        public bool $anon = false,
        public bool $personalRelease = false,
        public bool $internal = false,
        #[Between(0, 100)]
        public int $free = 0,
        public bool $refundable = false,
        #[Hidden]
        public ?string $keywords = null,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['tmdb_movie_id'] = \array_key_exists('movie_exists_on_tmdb', $properties) ? (($properties['tmdb_movie_id'] ?? 0) ?: null) : null;
        $properties['tmdb_tv_id'] = \array_key_exists('tv_exists_on_tmdb', $properties) ? (($properties['tmdb_tv_id'] ?? 0) ?: null) : null;
        $properties['imdb'] = \array_key_exists('title_exists_on_imdb', $properties) ? (($properties['imdb'] ?? 0) ?: null) : null;
        $properties['tvdb'] = \array_key_exists('tv_exists_on_tvdb', $properties) ? (($properties['tvdb'] ?? 0) ?: null) : null;
        $properties['mal'] = \array_key_exists('anime_exists_on_mal', $properties) ? (($properties['mal'] ?? 0) ?: null) : null;
        $properties['igdb'] = \array_key_exists('game_exists_on_igdb', $properties) ? (($properties['igdb'] ?? 0) ?: null) : null;
        $properties['mediainfo'] = TorrentTools::anonymizeMediainfo(($properties['mediainfo'] ?? '') ? (string) $properties['mediainfo'] : null);
        $properties['internal'] = $properties['internal'] ?? false;
        $properties['free'] = $properties['free'] ?? 0;
        $properties['refundable'] = $properties['refundable'] ?? false;

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<\Illuminate\Validation\ConditionalRules|\Illuminate\Validation\Rules\ExcludeIf|\Illuminate\Validation\Rules\RequiredIf|\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(#[RouteParameter('id')] int $id, Request $request): array
    {
        $category = Category::query()->findOrFail($request->integer('category_id'));

        $torrent = Torrent::query()->withoutGlobalScope(ApprovedScope::class)->find($id);
        $user = $request->user()->load('group')->loadExists('internals');

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
            'resolution_id' => [
                Rule::when($category->movie_meta || $category->tv_meta, 'required'),
                Rule::when(!$category->movie_meta && !$category->tv_meta, 'nullable'),
            ],
            'imdb' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:title_exists_on_imdb',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'tvdb' => [
                Rule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tvdb',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_movie_id' => [
                Rule::when($category->movie_meta, [
                    'required_with:movie_exists_on_tmdb',
                ]),
                Rule::when(!$category->movie_meta, [
                    $mustBeNull,
                ]),
            ],
            'tmdb_tv_id' => [
                Rule::when($category->tv_meta, [
                    'required_with:tv_exists_on_tmdb',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'mal' => [
                Rule::when($category->movie_meta || $category->tv_meta, [
                    'required_with:anime_exists_on_mal',
                ]),
                Rule::when(!($category->movie_meta || $category->tv_meta), [
                    $mustBeNull,
                ]),
            ],
            'igdb' => [
                Rule::when($category->game_meta, [
                    'required_with:game_exists_on_igdb',
                ]),
                Rule::when(!$category->game_meta, [
                    $mustBeNull,
                ]),
            ],
            'season_number' => [
                Rule::when($category->tv_meta, 'required'),
                Rule::when(!$category->tv_meta, 'nullable'),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, 'required'),
                Rule::when(!$category->tv_meta, 'nullable'),
            ],
            'anon' => [
                Rule::requiredIf($torrent->user_id === $user->id || $user->group->is_modo),
                Rule::excludeIf(!($torrent->user_id === $user->id || $user->group->is_modo)),
            ],
            'personal_release' => [
                Rule::requiredIf($torrent->user_id === $user->id || $user->group->is_modo),
                Rule::excludeIf(!($torrent->user_id === $user->id || $user->group->is_modo)),
            ],
            'internal' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'free' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
            'refundable' => [
                /** @phpstan-ignore property.notFound (Larastan doesn't yet support loadExists()) */
                Rule::excludeIf(!($user->group->is_modo || $user->internals_exists)),
            ],
        ];
    }
}
