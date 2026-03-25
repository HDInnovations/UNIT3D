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

use App\Models\Category;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TorrentRequest;

/** @extends Factory<TorrentRequest> */
class TorrentRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TorrentRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'          => $this->faker->name(),
            'category_id'   => Category::factory(),
            'imdb'          => $this->faker->randomNumber(),
            'tvdb'          => $this->faker->randomNumber(),
            'tmdb_movie_id' => $this->faker->randomNumber(),
            'tmdb_tv_id'    => $this->faker->randomNumber(),
            'mal'           => $this->faker->randomNumber(),
            'igdb'          => $this->faker->randomNumber(),
            'description'   => $this->faker->text(),
            'user_id'       => User::factory(),
            'bounty'        => $this->faker->randomFloat(),
            'anon'          => $this->faker->boolean(),
            'filled_by'     => User::factory(),
            'torrent_id'    => Torrent::factory(),
            'filled_when'   => $this->faker->dateTime(),
            'filled_anon'   => $this->faker->boolean(),
            'approved_by'   => User::factory(),
            'approved_when' => $this->faker->dateTime(),
            'type_id'       => Type::factory(),
            'resolution_id' => Resolution::factory(),
            'bumped_at'     => $this->faker->dateTime(),
        ];
    }
}
