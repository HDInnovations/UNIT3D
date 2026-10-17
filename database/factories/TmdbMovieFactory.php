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

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TmdbMovie;
use Override;

/** @extends Factory<TmdbMovie> */
class TmdbMovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbMovie::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'tmdb_id'           => $this->faker->randomDigitNotNull(),
            'imdb_id'           => $this->faker->randomDigitNotNull(),
            'title'             => $this->faker->sentence(),
            'title_sort'        => $this->faker->word(),
            'original_language' => $this->faker->word(),
            'adult'             => $this->faker->boolean(),
            'backdrop'          => $this->faker->word(),
            'budget'            => $this->faker->word(),
            'homepage'          => $this->faker->word(),
            'original_title'    => $this->faker->word(),
            'overview'          => $this->faker->text(),
            'popularity'        => $this->faker->word(),
            'poster'            => $this->faker->word(),
            'release_date'      => $this->faker->date(),
            'revenue'           => $this->faker->word(),
            'runtime'           => $this->faker->word(),
            'status'            => $this->faker->word(),
            'tagline'           => $this->faker->word(),
            'vote_average'      => $this->faker->word(),
            'vote_count'        => $this->faker->randomNumber(),
        ];
    }
}
