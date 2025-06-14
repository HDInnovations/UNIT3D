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
use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\Group;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('post in a topic creates a notification for the topic subscriber', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create();

    $topicOwner = User::factory()->create();
    $poster = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $topicOwnerNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $topicOwner->id,
        'block_notifications' => 1,
    ]);
    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 0,
        'show_subscription_topic' => 1,
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
    $topicSubscription = Subscription::factory()->create([
        'user_id'  => $subscriber->id,
        'forum_id' => $forum->id,
        'topic_id' => $topic->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => 'Test Post',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertSentTo(
        [$subscriber],
        NewPost::class
    );
});

test('post in a topic creates a notification for the topic subscriber when subscriber notifications are not disabled for specific group', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create();
    $randomGroup = Group::factory()->create();

    $topicOwner = User::factory()->create();
    $poster = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $topicOwnerNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $topicOwner->id,
        'block_notifications' => 1,
    ]);
    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                  => $subscriber->id,
        'block_notifications'      => 0,
        'show_subscription_topic'  => 1,
        'json_subscription_groups' => [$randomGroup->id],
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
    $topicSubscription = Subscription::factory()->create([
        'user_id'  => $subscriber->id,
        'forum_id' => $forum->id,
        'topic_id' => $topic->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => 'Test Post',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertSentTo(
        [$subscriber],
        NewPost::class
    );
});

test('post in a topic does not create a notification for the topic subscriber when all notifications disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create();

    $topicOwner = User::factory()->create();
    $poster = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $topicOwnerNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $topicOwner->id,
        'block_notifications' => 1,
    ]);
    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 1,
        'show_subscription_topic' => 1,
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
    $topicSubscription = Subscription::factory()->create([
        'user_id'  => $subscriber->id,
        'forum_id' => $forum->id,
        'topic_id' => $topic->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => 'Test Post',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertCount(0);
});

test('post in a topic does not create a notification for the topic subscriber when topic subscription notifications are disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create();

    $topicOwner = User::factory()->create();
    $poster = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $topicOwnerNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $topicOwner->id,
        'block_notifications' => 1,
    ]);
    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 0,
        'show_subscription_topic' => 0,
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
    $topicSubscription = Subscription::factory()->create([
        'user_id'  => $subscriber->id,
        'forum_id' => $forum->id,
        'topic_id' => $topic->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => 'Test Post',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertCount(0);
});

test('post in a topic does not create a notification for the topic subscriber when topic subscription notifications are disabled for specific group', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $group = Group::factory()->create();

    $topicOwner = User::factory()->create();
    $poster = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $topicOwnerNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $topicOwner->id,
        'block_notifications' => 1,
        'show_forum_topic'    => 1,
    ]);
    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                  => $subscriber->id,
        'block_notifications'      => 0,
        'show_subscription_topic'  => 1,
        'json_subscription_groups' => [$group->id],
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
    $topicSubscription = Subscription::factory()->create([
        'user_id'  => $subscriber->id,
        'forum_id' => $forum->id,
        'topic_id' => $topic->id,
    ]);
    $post = Post::factory()->create([
        'user_id'  => $topicOwner->id,
        'topic_id' => $topic->id,
        'content'  => 'First Post',
    ]);

    $response = $this->actingAs($poster)->post(route('posts.store'), [
        'topic_id' => $topic->id,
        'content'  => 'Test Post',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'topic_id' => $topic->id,
        'user_id'  => $poster->id,
    ]);

    Notification::assertCount(0);
});
