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

use App\Models\BonEarning;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<BonEarning> */
class BonEarningFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = BonEarning::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'position'    => $this->faker->numberBetween(1, 1000),
            'variable'    => $this->faker->randomElement(['1', 'age', 'size', 'seeders', 'leechers', 'times_completed', 'personal_release', 'internal', 'seedtime', 'connectable']),
            'multiplier'  => $this->faker->regexify('\d{1,2}\.\d{1,15}'),
            'operation'   => $this->faker->randomElement(['append', 'multiply']),
            'name'        => $this->faker->name(),
            'description' => $this->faker->sentence(),
        ];
    }
}
