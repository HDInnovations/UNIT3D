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

use App\Models\Apikey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Override;

/** @extends Factory<Apikey> */
class ApikeyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Apikey::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'name'          => $this->faker->name(),
            'content'       => Str::random(100),
            'can_upload'    => $this->faker->boolean(),
            'can_download'  => $this->faker->boolean(),
            'can_search'    => $this->faker->boolean(),
            'can_view_user' => $this->faker->boolean(),
            'expires_at'    => $this->faker->dateTime(),
        ];
    }
}
