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

use App\Models\TicketPriority;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    final public function run(): void
    {
        TicketPriority::query()->fillAndInsert([
            [
                'name'     => 'Low',
                'position' => 0,
                'color'    => '#FFDC00',
                'icon'     => config('other.font-awesome').' fa-circle',
            ],
            [
                'name'     => 'Medium',
                'position' => 1,
                'color'    => '#FF851B',
                'icon'     => config('other.font-awesome').' fa-circle',
            ],
            [
                'name'     => 'High',
                'position' => 2,
                'color'    => '#FF4136',
                'icon'     => config('other.font-awesome').' fa-circle',
            ],
        ]);
    }
}
