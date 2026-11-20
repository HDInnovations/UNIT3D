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

use App\Models\AutomaticTorrentFreeleech;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<AutomaticTorrentFreeleech> */
class AutomaticTorrentFreeleechFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = AutomaticTorrentFreeleech::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name_regex'           => $this->faker->text(),
            'position'             => $this->faker->numberBetween(1, 1000),
            'size'                 => $this->faker->numberBetween(1, 1_000_000_000),
            'category_id'          => Category::factory(),
            'type_id'              => Type::factory(),
            'resolution_id'        => Resolution::factory(),
            'freeleech_percentage' => $this->faker->numberBetween(0, 100),
        ];
    }
}
