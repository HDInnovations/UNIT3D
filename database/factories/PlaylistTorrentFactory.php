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

use App\Models\Playlist;
use App\Models\Torrent;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PlaylistTorrent;
use Override;

/** @extends Factory<PlaylistTorrent> */
class PlaylistTorrentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = PlaylistTorrent::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'position'    => $this->faker->randomNumber(),
            'playlist_id' => Playlist::factory(),
            'torrent_id'  => Torrent::factory(),
            'tmdb_id'     => $this->faker->unique()->randomDigitNotNull(),
        ];
    }
}
