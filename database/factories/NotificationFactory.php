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
use App\Models\Notification;
use Override;

/** @extends Factory<Notification> */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id'              => $this->faker->unique()->word(),
            'type'            => $this->faker->word(),
            'notifiable_id'   => $this->faker->randomDigitNotNull(),
            'notifiable_type' => $this->faker->word(),
            'data'            => $this->faker->text(),
            'read_at'         => $this->faker->dateTime(),
        ];
    }
}
