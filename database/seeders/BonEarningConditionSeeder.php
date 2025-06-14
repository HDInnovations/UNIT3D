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

use App\Models\BonEarningCondition;
use Illuminate\Database\Seeder;

class BonEarningConditionSeeder extends Seeder
{
    public function run(): void
    {
        BonEarningCondition::upsert([
            [
                'id'             => 1,
                'bon_earning_id' => 1,
                'operand1'       => 'seeders',
                'operator'       => '=',
                'operand2'       => 1,
            ],
            [
                'id'             => 2,
                'bon_earning_id' => 1,
                'operand1'       => 'times_completed',
                'operator'       => '>=',
                'operand2'       => 3,
            ],
            [
                'id'             => 3,
                'bon_earning_id' => 2,
                'operand1'       => 'age',
                'operator'       => '>=',
                'operand2'       => 12 * 30 * 24 * 3600,
            ],
            [
                'id'             => 4,
                'bon_earning_id' => 3,
                'operand1'       => 'age',
                'operator'       => '<',
                'operand2'       => 12 * 30 * 24 * 3600,
            ],
            [
                'id'             => 5,
                'bon_earning_id' => 3,
                'operand1'       => 'age',
                'operator'       => '>=',
                'operand2'       => 6 * 30 * 24 * 3600,
            ],
            [
                'id'             => 6,
                'bon_earning_id' => 4,
                'operand1'       => 'size',
                'operator'       => '>=',
                'operand2'       => 100 * 1024 * 1024 * 1024,
            ],
            [
                'id'             => 7,
                'bon_earning_id' => 5,
                'operand1'       => 'size',
                'operator'       => '<',
                'operand2'       => 100 * 1024 * 1024 * 1024,
            ],
            [
                'id'             => 8,
                'bon_earning_id' => 5,
                'operand1'       => 'size',
                'operator'       => '>=',
                'operand2'       => 25 * 1024 * 1024 * 1024,
            ],
            [
                'id'             => 9,
                'bon_earning_id' => 6,
                'operand1'       => 'size',
                'operator'       => '<',
                'operand2'       => 25 * 1024 * 1024 * 1024,
            ],
            [
                'id'             => 10,
                'bon_earning_id' => 6,
                'operand1'       => 'size',
                'operator'       => '>=',
                'operand2'       => 1 * 1024 * 1024 * 1024,
            ],
            [
                'id'             => 11,
                'bon_earning_id' => 7,
                'operand1'       => 'seedtime',
                'operator'       => '>=',
                'operand2'       => 12 * 30 * 24 * 3600,
            ],
            [
                'id'             => 12,
                'bon_earning_id' => 8,
                'operand1'       => 'seedtime',
                'operator'       => '<',
                'operand2'       => 12 * 30 * 24 * 3600,
            ],
            [
                'id'             => 13,
                'bon_earning_id' => 8,
                'operand1'       => 'seedtime',
                'operator'       => '>=',
                'operand2'       => 6 * 30 * 24 * 3600,
            ],
            [
                'id'             => 14,
                'bon_earning_id' => 9,
                'operand1'       => 'seedtime',
                'operator'       => '<',
                'operand2'       => 6 * 30 * 24 * 3600,
            ],
            [
                'id'             => 15,
                'bon_earning_id' => 9,
                'operand1'       => 'seedtime',
                'operator'       => '>=',
                'operand2'       => 3 * 30 * 24 * 3600,
            ],
            [
                'id'             => 16,
                'bon_earning_id' => 10,
                'operand1'       => 'seedtime',
                'operator'       => '<',
                'operand2'       => 3 * 30 * 24 * 3600,
            ],
            [
                'id'             => 17,
                'bon_earning_id' => 10,
                'operand1'       => 'seedtime',
                'operator'       => '>=',
                'operand2'       => 2 * 30 * 24 * 3600,
            ],
            [
                'id'             => 18,
                'bon_earning_id' => 11,
                'operand1'       => 'seedtime',
                'operator'       => '<',
                'operand2'       => 2 * 30 * 24 * 3600,
            ],
            [
                'id'             => 19,
                'bon_earning_id' => 11,
                'operand1'       => 'seedtime',
                'operator'       => '>=',
                'operand2'       => 1 * 30 * 24 * 3600,
            ],
        ], ['id']);
    }
}
