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
use App\Models\Application;
use Override;

/** @extends Factory<Application> */
class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'type'         => $this->faker->randomElement(['New to the game', 'Experienced with private trackers']),
            'email'        => $this->faker->unique()->email(),
            'referrer'     => $this->faker->text(),
            'status'       => $this->faker->boolean(),
            'moderated_at' => $this->faker->dateTime(),
            'moderated_by' => User::factory(),
            'accepted_by'  => User::factory(),
        ];
    }
}
