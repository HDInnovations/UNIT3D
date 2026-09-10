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

use App\Models\TmdbMovie;
use App\Models\Occupation;
use App\Models\TmdbPerson;
use App\Models\TmdbTv;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TmdbCredit;
use Override;

/** @extends Factory<TmdbCredit> */
class TmdbCreditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbCredit::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'tmdb_person_id' => TmdbPerson::factory(),
            'tmdb_movie_id'  => TmdbMovie::factory(),
            'tmdb_tv_id'     => TmdbTv::factory(),
            'occupation_id'  => Occupation::factory(),
            'order'          => $this->faker->randomNumber(),
            'character'      => $this->faker->unique()->word(),
        ];
    }
}
