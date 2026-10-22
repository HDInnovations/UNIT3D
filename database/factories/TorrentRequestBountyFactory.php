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

use App\Models\TorrentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TorrentRequestBounty;
use Override;

/** @extends Factory<TorrentRequestBounty> */
class TorrentRequestBountyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TorrentRequestBounty::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'seedbonus'  => $this->faker->randomFloat(),
            'anon'       => $this->faker->boolean(),
            'request_id' => TorrentRequest::factory(),
        ];
    }
}
