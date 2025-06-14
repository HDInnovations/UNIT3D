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

use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\Group;
use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\Topic;
use App\Models\Post;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewPostTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('user tags user in forum topic creates a notification for tagged user', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
    ]);

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 0,
        'show_mention_forum_post' => 1,
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertSentToTimes(
        $user,
        NewPostTag::class,
        1,
    );
});

test('user tags user in forum topic creates a notification for tagged user when post mention notifications are not disabled for specific group', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
    ]);
    $randomGroup = Group::factory()->create();

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 0,
        'show_mention_forum_post' => 1,
        'json_mention_groups'     => [$randomGroup->id],
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertSentToTimes(
        $user,
        NewPostTag::class,
        1,
    );
});

test('user tags user in forum topic does not create a notification for tagged user when all notifications disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
    ]);

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 1,
        'show_mention_forum_post' => 1,
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertNotSentTo(
        [$user],
        NewPostTag::class
    );
});

test('staff tags user in forum topic creates a notification for tagged user even when post mention notifications are disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 0,
        'show_mention_forum_post' => 0,
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertSentToTimes(
        $user,
        NewPostTag::class,
        1,
    );
});

test('user tags user in forum topic does not create a notification for tagged user when post mention notifications are disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
    ]);

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 0,
        'show_mention_forum_post' => 0,
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertNotSentTo(
        [$user],
        NewPostTag::class
    );
});

test('user tags user in forum topic does not create a notification for tagged user when post mention notifications are not disabled for specific group', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create([
        'can_comment' => true,
    ]);

    $user = User::factory()->create([
        'username'    => 'TestUsername',
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $poster = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $topicOwner = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $user->id,
        'block_notifications'     => 0,
        'show_mention_forum_post' => 1,
        'json_mention_groups'     => [$group->id],
    ]);

    $forum = Forum::factory()->create();
    $forumPermissions = ForumPermission::factory()->create([
        'forum_id'    => $forum->id,
        'group_id'    => $group->id,
        'read_topic'  => 1,
        'reply_topic' => 1,
    ]);
    $topic = Topic::factory()->create([
        'forum_id'           => $forum->id,
        'state'              => 'open',
        'first_post_user_id' => $topicOwner->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => '@'.$user->username.' Test',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertNotSentTo(
        [$user],
        NewPostTag::class
    );
});
