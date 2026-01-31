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

use App\Enums\ModerationStatus;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Torrent> */
class TorrentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Torrent::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $freeleech = ['0', '25', '50', '75', '100'];
        $selected = random_int(0, \count($freeleech) - 1);

        return [
            'name'            => $this->faker->name(),
            'description'     => $this->faker->text(),
            'mediainfo'       => $this->faker->text(),
            'info_hash'       => $this->faker->asciify('********************'),
            'file_name'       => $this->faker->word(),
            'num_file'        => $this->faker->randomNumber(),
            'size'            => $this->faker->randomFloat(),
            'nfo'             => '',
            'leechers'        => $this->faker->randomNumber(),
            'seeders'         => $this->faker->randomNumber(),
            'times_completed' => $this->faker->randomNumber(),
            'category_id'     => fn () => Category::factory()->create()->id,
            'user_id'         => fn () => User::factory()->create()->id,
            'imdb'            => $this->faker->randomNumber(),
            'tvdb'            => $this->faker->randomNumber(),
            'tmdb_movie_id'   => $this->faker->randomNumber(),
            'tmdb_tv_id'      => $this->faker->randomNumber(),
            'mal'             => $this->faker->randomNumber(),
            'igdb'            => $this->faker->randomNumber(),
            'type_id'         => fn () => Type::factory()->create()->id,
            'resolution_id'   => fn () => Resolution::factory()->create()->id,
            'free'            => $freeleech[$selected],
            'doubleup'        => $this->faker->boolean(),
            'highspeed'       => $this->faker->boolean(),
            'status'          => ModerationStatus::APPROVED,
            'moderated_at'    => now(),
            'moderated_by'    => User::factory(),
            'anon'            => $this->faker->boolean(),
            'sticky'          => $this->faker->boolean(),
            'internal'        => $this->faker->boolean(),
        ];
    }
}
