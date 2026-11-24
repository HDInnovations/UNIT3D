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
use App\Models\Keyword;
use Override;

/** @extends Factory<Keyword> */
class KeywordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Keyword::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name'       => $this->faker->unique()->name(),
            'torrent_id' => $this->faker->unique()->randomDigitNotNull(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
