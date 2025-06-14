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

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        Type::upsert([
            [
                'id'       => 1,
                'name'     => 'Full Disc',
                'position' => 0,
            ],
            [
                'id'       => 2,
                'name'     => 'Remux',
                'position' => 1,
            ],
            [
                'id'       => 3,
                'name'     => 'Encode',
                'position' => 2,
            ],
            [
                'id'       => 4,
                'name'     => 'WEB-DL',
                'position' => 4,
            ],
            [
                'id'       => 5,
                'name'     => 'WEBRip',
                'position' => 5,
            ],
            [
                'id'       => 6,
                'name'     => 'HDTV',
                'position' => 6,
            ],
        ], ['id'], []);
    }
}
