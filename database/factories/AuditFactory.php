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
use App\Models\Audit;
use JsonException;

/** @extends Factory<Audit> */
class AuditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Audit::class;

    /**
     * Define the model's default state.
     *
     * @throws JsonException
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'auditable_type' => $this->faker->word(),
            'auditable_id'   => $this->faker->randomDigitNotNull(),
            'action'         => $this->faker->word(),
            'record'         => json_encode(["key" => "value"], JSON_THROW_ON_ERROR),
        ];
    }
}
