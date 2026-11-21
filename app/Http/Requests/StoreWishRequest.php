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

use App\Models\Wish;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\RequiredWithout;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\Constraints\WhereConstraint;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreWishRequest extends Data
{
    public function __construct(
        #[Min(1)]
        public ?int $tmdbMovieId,
        #[Min(1)]
        public ?int $tmdbTvId,
        #[In('movie', 'tv')]
        public string $meta,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'tmdb_movie_id' => [
                new Unique(Wish::class, where: new WhereConstraint('user_id', auth()->id())),
                new RequiredIf('meta', 'movie'),
                new RequiredWithout('tmdb_tv_id'),
            ],
            'tmdb_tv_id' => [
                new Unique(Wish::class, where: new WhereConstraint('user_id', auth()->id())),
                new RequiredIf('meta', 'tv'),
                new RequiredWithout('tmdb_movie_id'),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'tmdb_movie_id.unique' => 'You are already receiving notifications for this movie.',
            'tmdb_tv_id.unique'    => 'You are already receiving notifications for this tv.',
        ];
    }
}
