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

namespace Database\Seeders;

use App\Models\Resolution;
use Illuminate\Database\Seeder;

class ResolutionSeeder extends Seeder
{
    public function run(): void
    {
        Resolution::upsert([
            [
                'id'       => 1,
                'name'     => '4320p',
                'position' => 0,
            ],
            [
                'id'       => 2,
                'name'     => '2160p',
                'position' => 1,
            ],
            [
                'id'       => 3,
                'name'     => '1080p',
                'position' => 2,
            ],
            [
                'id'       => 4,
                'name'     => '1080i',
                'position' => 4,
            ],
            [
                'id'       => 5,
                'name'     => '720p',
                'position' => 5,
            ],
            [
                'id'       => 6,
                'name'     => '576p',
                'position' => 6,
            ],
            [
                'id'       => 7,
                'name'     => '576i',
                'position' => 7,
            ],
            [
                'id'       => 8,
                'name'     => '480p',
                'position' => 8,
            ],
            [
                'id'       => 9,
                'name'     => '480i',
                'position' => 9,
            ],
            [
                'id'       => 10,
                'name'     => 'Other',
                'position' => 10,
            ],
        ], ['id'], []);
    }
}
