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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace Database\Factories;

use App\Models\UploadContest;
use App\Models\UploadContestWinner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UploadContestWinner> */
class UploadContestWinnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = UploadContestWinner::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $types = ['fl_tokens', 'bon'];

        return [
            'upload_contest_id' => UploadContest::factory(),
            'user_id'           => User::factory(),
            'place_number'      => $this->faker->numberBetween(1, 10),
            'uploads'           => $this->faker->numberBetween(1, 100),
        ];
    }
}
