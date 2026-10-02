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

use App\Models\Torrent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Peer;
use Override;

/** @extends Factory<Peer> */
class PeerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Peer::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'peer_id'     => $this->faker->unique()->randomDigitNotNull(),
            'ip'          => $this->faker->word(),
            'port'        => $this->faker->randomNumber(),
            'agent'       => $this->faker->word(),
            'uploaded'    => $this->faker->randomNumber(),
            'downloaded'  => $this->faker->randomNumber(),
            'left'        => $this->faker->randomNumber(),
            'seeder'      => $this->faker->boolean(),
            'torrent_id'  => Torrent::factory(),
            'user_id'     => User::factory(),
            'connectable' => $this->faker->boolean(),
            'torrents.id' => Torrent::factory(),
        ];
    }
}
