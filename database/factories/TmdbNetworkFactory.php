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
use App\Models\TmdbNetwork;

/** @extends Factory<TmdbNetwork> */
class TmdbNetworkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbNetwork::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'           => $this->faker->name(),
            'description'    => $this->faker->text(),
            'logo'           => $this->faker->word(),
            'homepage'       => $this->faker->word(),
            'headquarters'   => $this->faker->word(),
            'origin_country' => $this->faker->word(),
        ];
    }
}
