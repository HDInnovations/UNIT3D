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
use App\Models\DonationPackage;
use Override;

/** @extends Factory<DonationPackage> */
class DonationPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = DonationPackage::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'position'     => $this->faker->randomDigitNotNull(),
            'name'         => $this->faker->name(),
            'description'  => $this->faker->sentence(3),
            'cost'         => $this->faker->randomFloat(2, 10, 100),   // Generates a random float with 2 decimal places between 10 and 100
            'upload_value' => $this->faker->randomNumber(),
            'invite_value' => $this->faker->randomNumber(),
            'bonus_value'  => $this->faker->randomNumber(),
            'donor_value'  => $this->faker->numberBetween(30, 365),
            'is_active'    => $this->faker->boolean(),
        ];
    }
}
