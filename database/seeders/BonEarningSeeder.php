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

use App\Models\BonEarning;
use Illuminate\Database\Seeder;

class BonEarningSeeder extends Seeder
{
    public function run(): void
    {
        BonEarning::upsert([
            [
                'id'          => 1,
                'position'    => 1,
                'variable'    => '1',
                'multiplier'  => 2,
                'operation'   => 'append',
                'name'        => 'Dying Torrent',
                'description' => 'You are the last remaining seeder! (has been downloaded at least 3 times)',
            ],
            [
                'id'          => 2,
                'position'    => 2,
                'variable'    => '1',
                'multiplier'  => 1.5,
                'operation'   => 'append',
                'name'        => 'Legendary Torrent',
                'description' => 'Older than 12 months',
            ],
            [
                'id'          => 3,
                'position'    => 3,
                'variable'    => '1',
                'multiplier'  => 1,
                'operation'   => 'append',
                'name'        => 'Old Torrent',
                'description' => 'Older than 6 months',
            ],
            [
                'id'          => 4,
                'position'    => 4,
                'variable'    => '1',
                'multiplier'  => 0.75,
                'operation'   => 'append',
                'name'        => 'Huge Torrent',
                'description' => 'Torrent Size ≥ 100 GiB',
            ],
            [
                'id'          => 5,
                'position'    => 5,
                'variable'    => '1',
                'multiplier'  => 0.5,
                'operation'   => 'append',
                'name'        => 'Large Torrent',
                'description' => 'Torrent Size ≥ 25 GiB but < 100 GiB',
            ],
            [
                'id'          => 6,
                'position'    => 6,
                'variable'    => '1',
                'multiplier'  => 0.25,
                'operation'   => 'append',
                'name'        => 'Everyday Torrent',
                'description' => 'Torrent Size ≥ 1 GiB but < 25 GiB',
            ],
            [
                'id'          => 7,
                'position'    => 7,
                'variable'    => '1',
                'multiplier'  => 2,
                'operation'   => 'append',
                'name'        => 'Legendary Seeder',
                'description' => 'Seed Time ≥ 1 year',
            ],
            [
                'id'          => 8,
                'position'    => 8,
                'variable'    => '1',
                'multiplier'  => 1,
                'operation'   => 'append',
                'name'        => 'MVP Seeder',
                'description' => 'Seed Time ≥ 6 months but < 1 year',
            ],
            [
                'id'          => 9,
                'position'    => 9,
                'variable'    => '1',
                'multiplier'  => 0.75,
                'operation'   => 'append',
                'name'        => 'Committed Seeder',
                'description' => 'Seed Time ≥ 3 months but < 6 months',
            ],
            [
                'id'          => 10,
                'position'    => 10,
                'variable'    => '1',
                'multiplier'  => 0.5,
                'operation'   => 'append',
                'name'        => 'Team Player Seeder',
                'description' => 'Seed Time ≥ 2 months but < 3 months',
            ],
            [
                'id'          => 11,
                'position'    => 11,
                'variable'    => '1',
                'multiplier'  => 0.25,
                'operation'   => 'append',
                'name'        => 'Participant Seeder',
                'description' => 'Seed Time ≥ 1 month but < 2 months',
            ],
        ], ['id']);
    }
}
