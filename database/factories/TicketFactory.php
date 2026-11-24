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

use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use Override;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => TicketCategory::factory(),
            'priority_id' => TicketPriority::factory(),
            'staff_id'    => User::factory(),
            'user_read'   => $this->faker->boolean(),
            'staff_read'  => $this->faker->boolean(),
            'subject'     => $this->faker->word(),
            'body'        => $this->faker->text(),
            'closed_at'   => $this->faker->dateTime(),
        ];
    }
}
