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
use App\Models\TmdbTv;
use Override;

/** @extends Factory<TmdbTv> */
class TmdbTvFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbTv::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'tmdb_id'                 => $this->faker->randomDigitNotNull(),
            'imdb_id'                 => $this->faker->randomDigitNotNull(),
            'tvdb_id'                 => $this->faker->randomDigitNotNull(),
            'type'                    => $this->faker->word(),
            'name'                    => $this->faker->name(),
            'name_sort'               => $this->faker->word(),
            'overview'                => $this->faker->text(),
            'number_of_episodes'      => $this->faker->randomNumber(),
            'count_existing_episodes' => $this->faker->randomNumber(),
            'count_total_episodes'    => $this->faker->randomNumber(),
            'number_of_seasons'       => $this->faker->randomNumber(),
            'episode_run_time'        => $this->faker->word(),
            'first_air_date'          => $this->faker->word(),
            'status'                  => $this->faker->word(),
            'homepage'                => $this->faker->word(),
            'in_production'           => $this->faker->boolean(),
            'last_air_date'           => $this->faker->word(),
            'next_episode_to_air'     => $this->faker->word(),
            'origin_country'          => $this->faker->word(),
            'original_language'       => $this->faker->word(),
            'original_name'           => $this->faker->word(),
            'popularity'              => $this->faker->word(),
            'backdrop'                => $this->faker->word(),
            'poster'                  => $this->faker->word(),
            'vote_average'            => $this->faker->word(),
            'vote_count'              => $this->faker->randomNumber(),
        ];
    }
}
