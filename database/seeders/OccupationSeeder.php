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

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    public function run(): void
    {
        Occupation::query()->upsert([
            [
                'id'       => 1,
                'position' => 1,
                'name'     => 'Creator',
            ],
            [
                'id'       => 2,
                'position' => 2,
                'name'     => 'Director',
            ],
            [
                'id'       => 3,
                'position' => 3,
                'name'     => 'Writer',
            ],
            [
                'id'       => 4,
                'position' => 4,
                'name'     => 'Producer',
            ],
            [
                'id'       => 5,
                'position' => 5,
                'name'     => 'Composer',
            ],
            [
                'id'       => 6,
                'position' => 6,
                'name'     => 'Cinematographer',
            ],
            [
                'id'       => 7,
                'position' => 7,
                'name'     => 'Editor',
            ],
            [
                'id'       => 8,
                'position' => 8,
                'name'     => 'Production Designer',
            ],
            [
                'id'       => 9,
                'position' => 9,
                'name'     => 'Art Director',
            ],
            [
                'id'       => 10,
                'position' => 10,
                'name'     => 'Actor',
            ],
        ], ['id'], [
            'position',
            'name',
        ]);
    }
}
