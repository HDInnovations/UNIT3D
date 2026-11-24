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
use App\Models\TmdbPerson;
use Override;

/** @extends Factory<TmdbPerson> */
class TmdbPersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbPerson::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name'                 => $this->faker->name(),
            'imdb_id'              => $this->faker->randomDigitNotNull(),
            'known_for_department' => $this->faker->word(),
            'place_of_birth'       => $this->faker->word(),
            'popularity'           => $this->faker->word(),
            'profile'              => $this->faker->word(),
            'still'                => $this->faker->word(),
            'adult'                => $this->faker->word(),
            'also_known_as'        => $this->faker->text(),
            'biography'            => $this->faker->text(),
            'birthday'             => $this->faker->word(),
            'deathday'             => $this->faker->word(),
            'gender'               => $this->faker->word(),
            'homepage'             => $this->faker->word(),
        ];
    }
}
