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

use App\Models\BonExchange;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BonTransactions;
use Override;

/** @extends Factory<BonTransactions> */
class BonTransactionsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = BonTransactions::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'bon_exchange_id' => BonExchange::factory(),
            'name'            => $this->faker->name(),
            'cost'            => $this->faker->randomFloat(),
            'sender_id'       => User::factory(),
            'receiver_id'     => User::factory(),
            'created_at'      => $this->faker->dateTime(),
        ];
    }
}
