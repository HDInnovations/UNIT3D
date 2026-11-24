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

namespace Database\Factories;

use App\Models\Giveaway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<Giveaway> */
class GiveawayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Giveaway::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name'        => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'icon'        => $this->faker->imageUrl(),
            'active'      => $this->faker->boolean(),
            'starts_at'   => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'ends_at'     => $this->faker->dateTimeBetween('+1 month', '+2 months'),
        ];
    }
}
