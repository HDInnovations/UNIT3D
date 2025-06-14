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

use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\User;
use Database\Seeders\GroupSeeder;
use Database\Seeders\UserSeeder;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('forums.index'));
    $response->assertOk();
    $response->assertViewIs('forum.index');
    $response->assertViewHas('categories');
    $response->assertViewHas('num_posts');
    $response->assertViewHas('num_forums');
    $response->assertViewHas('num_topics');
});

test('show returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create();

    $forum = Forum::factory()->create([
        'last_post_user_id' => $user->id,
        'last_topic_id'     => null,
    ]);

    ForumPermission::factory()->create([
        'group_id'   => $user->group_id,
        'forum_id'   => $forum->id,
        'read_topic' => true,
    ]);

    $response = $this->actingAs($user)->get(route('forums.show', ['id' => $forum->id]));
    $response->assertViewIs('forum.forum-topic.index');
});
