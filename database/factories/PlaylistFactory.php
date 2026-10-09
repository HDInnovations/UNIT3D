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

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Playlist;
use App\Models\PlaylistCategory;
use Override;

/** @extends Factory<Playlist> */
class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'              => User::factory(),
            'playlist_category_id' => PlaylistCategory::factory(),
            'name'                 => $this->faker->name(),
            'description'          => $this->faker->text(),
            'cover_image'          => $this->faker->word(),
            'position'             => $this->faker->randomNumber(),
            'is_private'           => $this->faker->boolean(),
            'is_pinned'            => $this->faker->boolean(),
            'is_featured'          => $this->faker->boolean(),
        ];
    }
}
