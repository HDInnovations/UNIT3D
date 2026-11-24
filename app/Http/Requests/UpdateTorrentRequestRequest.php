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
use App\Models\Resolution;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Override;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class UpdateTorrentRequestRequest extends Data
{
    public function __construct(
        #[Max(180)]
        public string $name,
        #[Min(0)]
        public ?int $imdb,
        #[Min(0)]
        public ?int $tvdb,
        #[Min(0)]
        public ?int $tmdbMovieId,
        #[Min(0)]
        public ?int $tmdbTvId,
        #[Min(0)]
        public ?int $mal,
        #[Min(0)]
        public ?int $igdb,
        #[Min(0)]
        public ?int $seasonNumber,
        #[Min(0)]
        public ?int $episodeNumber,
        #[Exists(Category::class, 'id')]
        public int $categoryId,
        #[Exists(Type::class, 'id')]
        public ?int $typeId,
        #[Exists(Resolution::class, 'id')]
        public ?int $resolutionId,
        public string $description,
        public bool $anon,
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
        $properties['tmdb_movie_id'] = \array_key_exists('movie_exists_on_tmdb', $properties) ? ($properties['tmdb_movie_id'] ?: null) : null;
        $properties['tmdb_tv_id'] = \array_key_exists('tv_exists_on_tmdb', $properties) ? ($properties['tmdb_tv_id'] ?: null) : null;
        $properties['imdb'] = \array_key_exists('title_exists_on_imdb', $properties) ? ($properties['imdb'] ?: null) : null;
        $properties['tvdb'] = \array_key_exists('tv_exists_on_tvdb', $properties) ? ($properties['tvdb'] ?: null) : null;
        $properties['mal'] = \array_key_exists('anime_exists_on_mal', $properties) ? ($properties['mal'] ?: null) : null;
        $properties['igdb'] = \array_key_exists('game_exists_on_igdb', $properties) ? ($properties['igdb'] ?: null) : null;

        return $properties;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\ConditionalRules|string>|string>
     */
    public static function rules(Request $request): array
    {
        $category = Category::query()->findOrFail($request->integer('category_id'));

        $mustBeNull = function (string $attribute, mixed $value, callable $fail): void {
            if ($value !== null) {
                $fail("The {$attribute} must be null.");
            }
        };

        return [
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
            'season_number' => [
                Rule::when($category->tv_meta, [
                    'required',
                ]),
                Rule::when(!$category->tv_meta, [
                    $mustBeNull,
                ]),
            ],
            'episode_number' => [
                Rule::when($category->tv_meta, [
                    'required',
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
        ];
    }
}
