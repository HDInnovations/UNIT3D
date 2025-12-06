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

use App\Models\UploadContest;
use App\Models\UploadContestPrize;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UploadContestPrize> */
class UploadContestPrizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = UploadContestPrize::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $types = ['fl_tokens', 'bon'];

        return [
            'upload_contest_id' => UploadContest::factory(),
            'type'              => $this->faker->randomElement($types),
            'amount'            => $this->faker->numberBetween(1, 1000),
            'position'          => $this->faker->numberBetween(1, 10),
        ];
    }
}
