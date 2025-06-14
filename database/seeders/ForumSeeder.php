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

use App\Models\Forum;
use App\Models\ForumCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        ForumCategory::upsert([
            [
                'id'          => 1,
                'position'    => 1,
                'name'        => 'UNIT3D Forums',
                'slug'        => 'unit3d-forums',
                'description' => 'UNIT3D Forums',
                'created_at'  => '2017-01-03 18:29:21',
                'updated_at'  => '2017-01-03 18:29:21',
            ],
        ], ['id'], []);

        Forum::upsert([
            [
                'id'                   => 1,
                'position'             => 2,
                'num_topic'            => null,
                'num_post'             => null,
                'last_topic_id'        => null,
                'last_post_id'         => null,
                'last_post_user_id'    => null,
                'last_post_created_at' => null,
                'name'                 => 'Welcome',
                'slug'                 => 'welcome',
                'description'          => 'Introduce Yourself Here!',
                'forum_category_id'    => 1,
                'created_at'           => '2017-04-01 20:16:06',
                'updated_at'           => '2017-12-27 18:19:07',
            ],
        ], ['id'], ['updated_at' => DB::raw('updated_at')]);
    }
}
