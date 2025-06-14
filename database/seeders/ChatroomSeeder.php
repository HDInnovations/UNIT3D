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

use App\Models\Chatroom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatroomSeeder extends Seeder
{
    public function run(): void
    {
        Chatroom::upsert([
            [
                'name' => 'General',
            ],
        ], ['name'], ['updated_at' => DB::raw('updated_at')]);
    }
}
