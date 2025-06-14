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

use App\Models\ForumPermission;
use Illuminate\Database\Seeder;

class ForumPermissionSeeder extends Seeder
{
    public function run(): void
    {
        ForumPermission::upsert([
            [
                'id'          => 1,
                'forum_id'    => 1,
                'group_id'    => 1,
                'read_topic'  => false,
                'reply_topic' => false,
                'start_topic' => false,
            ],
            [
                'id'          => 2,
                'forum_id'    => 1,
                'group_id'    => 2,
                'read_topic'  => false,
                'reply_topic' => false,
                'start_topic' => false,
            ],
            [
                'id'          => 3,
                'forum_id'    => 1,
                'group_id'    => 3,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 4,
                'forum_id'    => 1,
                'group_id'    => 4,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 5,
                'forum_id'    => 1,
                'group_id'    => 5,
                'read_topic'  => false,
                'reply_topic' => false,
                'start_topic' => false,
            ],
            [
                'id'          => 6,
                'forum_id'    => 1,
                'group_id'    => 6,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 7,
                'forum_id'    => 1,
                'group_id'    => 7,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 8,
                'forum_id'    => 1,
                'group_id'    => 8,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 9,
                'forum_id'    => 1,
                'group_id'    => 9,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 10,
                'forum_id'    => 1,
                'group_id'    => 10,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 11,
                'forum_id'    => 1,
                'group_id'    => 11,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 12,
                'forum_id'    => 1,
                'group_id'    => 12,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 13,
                'forum_id'    => 1,
                'group_id'    => 13,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 14,
                'forum_id'    => 1,
                'group_id'    => 14,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 15,
                'forum_id'    => 1,
                'group_id'    => 15,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 16,
                'forum_id'    => 1,
                'group_id'    => 16,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 17,
                'forum_id'    => 1,
                'group_id'    => 17,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 18,
                'forum_id'    => 1,
                'group_id'    => 18,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 37,
                'forum_id'    => 1,
                'group_id'    => 19,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
            [
                'id'          => 39,
                'forum_id'    => 1,
                'group_id'    => 20,
                'read_topic'  => false,
                'reply_topic' => false,
                'start_topic' => false,
            ],
            [
                'id'          => 40,
                'forum_id'    => 1,
                'group_id'    => 21,
                'read_topic'  => false,
                'reply_topic' => false,
                'start_topic' => false,
            ],
            [
                'id'          => 41,
                'forum_id'    => 1,
                'group_id'    => 22,
                'read_topic'  => true,
                'reply_topic' => true,
                'start_topic' => true,
            ],
        ], ['id'], []);
    }
}
