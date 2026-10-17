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

use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/** @extends Factory<Invite> */
class InviteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Invite::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'email'       => $this->faker->safeEmail(),
            'code'        => $this->faker->unique()->lexify(),
            'expires_on'  => $this->faker->dateTimeBetween('now', '+1 month'),
            'accepted_by' => null,
            'accepted_at' => null,
            'custom'      => $this->faker->text(),
        ];
    }

    public function expired(): self
    {
        return $this->state(fn (array $_attributes) => [
            'expires_on' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    public function accepted(): self
    {
        return $this->state(fn (array $_attributes) => [
            'accepted_by' => User::factory(),
            'accepted_at' => $this->faker->dateTimeBetween('-1 month'),
        ]);
    }
}
