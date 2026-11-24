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

use App\Models\InternalUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Internal;
use Override;

/** @extends Factory<InternalUser> */
class InternalUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = InternalUser::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'internal_id' => Internal::factory(),
            'position'    => $this->faker->numberBetween(1, 1000),
        ];
    }
}
