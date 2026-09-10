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
use App\Models\Category;
use Override;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'image'      => null,
            'position'   => $this->faker->randomNumber(),
            'icon'       => $this->faker->word(),
            'no_meta'    => $this->faker->boolean(),
            'music_meta' => $this->faker->boolean(),
            'game_meta'  => $this->faker->boolean(),
            'tv_meta'    => $this->faker->boolean(),
            'movie_meta' => $this->faker->boolean(),
        ];
    }
}
