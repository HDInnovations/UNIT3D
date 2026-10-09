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

use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Report;
use Override;

/** @extends Factory<Report> */
class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'type'                => $this->faker->word(),
            'title'               => $this->faker->sentence(),
            'reporter_id'         => User::factory(),
            'reported_user_id'    => User::factory(),
            'reported_torrent_id' => Torrent::factory(),
            'reported_request_id' => TorrentRequest::factory(),
            'message'             => $this->faker->text(),
            'assigned_to'         => User::factory(),
            'verdict'             => $this->faker->text(),
            'created_at'          => $this->faker->dateTime(),
            'updated_at'          => $this->faker->optional()->dateTime(),
        ];
    }
}
