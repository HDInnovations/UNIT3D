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
use App\Models\GiveawayPrize;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<GiveawayPrize> */
class GiveawayPrizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = GiveawayPrize::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'giveaway_id' => Giveaway::factory(),
            'type'        => $this->faker->randomElement(['fl_tokens', 'bon']),
            'min'         => $this->faker->numberBetween(1, 10),
            'max'         => $this->faker->numberBetween(11, 20),
            'weight'      => $this->faker->numberBetween(10),
        ];
    }
}
