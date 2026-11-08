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

use App\Models\BlockedIp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<BlockedIp> */
class BlockedIpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = BlockedIp::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->ipv4(),
            'reason'     => $this->faker->sentence(),
            'user_id'    => User::factory(),
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
        ];
    }
}
