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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace Database\Factories;

use App\Models\CollectibleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CollectibleCategory> */
class CollectibleCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CollectibleCategory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'     => $this->faker->word(),
            'position' => $this->faker->randomNumber(),
        ];
    }
}
