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

use App\Http\Middleware\UpdateLastAction;
use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\Group;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\GroupSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

beforeEach(function (): void {
    $this->withoutMiddleware(UpdateLastAction::class);
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);
});

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

test('invite forums are hidden when invite privileges are revoked', function (): void {
    $group = Group::factory()->create(['can_invite' => true]);
    $user = User::factory()->create([
        'group_id'   => $group->id,
        'can_invite' => false,
    ]);

    $forum = Forum::factory()->create([
        'is_invite_forum' => true,
        'name'            => 'Invite Offers',
    ]);

    ForumPermission::factory()->create([
        'group_id'   => $user->group_id,
        'forum_id'   => $forum->id,
        'read_topic' => true,
    ]);

    $this->actingAs($user)
        ->get(route('forums.index'))
        ->assertOk()
        ->assertDontSee('Invite Offers');

    $this->actingAs($user)
        ->get(route('forums.show', ['id' => $forum->id]))
        ->assertNotFound();
});

test('invite forums remain visible when invite privileges are allowed', function (): void {
    $group = Group::factory()->create(['can_invite' => true]);
    $user = User::factory()->create([
        'group_id'   => $group->id,
        'can_invite' => null,
    ]);

    $forum = Forum::factory()->create(['is_invite_forum' => true]);

    ForumPermission::factory()->create([
        'group_id'   => $user->group_id,
        'forum_id'   => $forum->id,
        'read_topic' => true,
    ]);

    $this->actingAs($user)
        ->get(route('forums.show', ['id' => $forum->id]))
        ->assertOk()
        ->assertViewIs('forum.forum-topic.index');
});

test('invite forum topics and posts are hidden when invite privileges are revoked', function (): void {
    $group = Group::factory()->create(['can_invite' => true]);
    $user = User::factory()->create([
        'group_id'   => $group->id,
        'can_invite' => false,
    ]);

    $forum = Forum::factory()->create(['is_invite_forum' => true]);
    $topic = Topic::factory()->create([
        'forum_id' => $forum->id,
        'state'    => 'open',
    ]);
    $post = Post::factory()->create(['topic_id' => $topic->id]);

    ForumPermission::factory()->create([
        'group_id'    => $user->group_id,
        'forum_id'    => $forum->id,
        'read_topic'  => true,
        'reply_topic' => true,
        'start_topic' => true,
    ]);

    $this->actingAs($user)
        ->get(route('topics.show', ['id' => $topic->id]))
        ->assertNotFound();

    expect(Post::query()->authorized(canReadTopic: true)->pluck('id')->all())
        ->not->toContain($post->id);
});
