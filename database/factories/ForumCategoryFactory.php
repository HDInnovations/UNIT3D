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

use App\Models\ForumCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;

/** @extends Factory<ForumCategory> */
class ForumCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = ForumCategory::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'position'    => $this->faker->numberBetween(0, 65535),
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->text(),
        ];
    }
}
