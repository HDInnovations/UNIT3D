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
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('create a topic in a subscribed forum creates a notification for the subscriber', function (): void {
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

    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 0,
        'show_subscription_forum' => 1,
    ]);

    $forum = Forum::factory()->create();
    $forumPermission = ForumPermission::factory()->create([
        'group_id'    => $group->id,
        'forum_id'    => $forum->id,
        'start_topic' => true,
        'read_topic'  => true,
    ]);
    $forumSubscription = Subscription::factory()->create([
        'forum_id' => $forum->id,
        'topic_id' => null,
        'user_id'  => $subscriber->id,
    ]);

    $postData = [
        'title'   => 'Sample Topic Title',
        'content' => 'This is the test content.',
    ];

    $response = $this->actingAs($user)->post(route('topics.store', ['id' => $forum->id]), [
        'title'   => $postData['title'],
        'content' => $postData['content'],
    ]);

    $this->assertDatabaseHas('topics', [
        'name'               => $postData['title'],
        'first_post_user_id' => $user->id,
        'forum_id'           => $forum->id,
    ]);

    Notification::assertSentTo(
        [$subscriber],
        NewTopic::class
    );
    Notification::assertCount(1);
});

test('create a topic in a subscribed forum creates a notification for the subscriber when subscribe notifications not disabled for specific group', function (): void {
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

    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                  => $subscriber->id,
        'block_notifications'      => 0,
        'show_subscription_forum'  => 1,
        'json_subscription_groups' => [$randomGroup->id],
    ]);

    $forum = Forum::factory()->create();
    $forumPermission = ForumPermission::factory()->create([
        'group_id'    => $group->id,
        'forum_id'    => $forum->id,
        'start_topic' => true,
        'read_topic'  => true,
    ]);
    $forumSubscription = Subscription::factory()->create([
        'forum_id' => $forum->id,
        'topic_id' => null,
        'user_id'  => $subscriber->id,
    ]);

    $postData = [
        'title'   => 'Sample Topic Title',
        'content' => 'This is the test content.',
    ];

    $response = $this->actingAs($user)->post(route('topics.store', ['id' => $forum->id]), [
        'title'   => $postData['title'],
        'content' => $postData['content'],
    ]);

    $this->assertDatabaseHas('topics', [
        'name'               => $postData['title'],
        'first_post_user_id' => $user->id,
        'forum_id'           => $forum->id,
    ]);

    Notification::assertSentTo(
        [$subscriber],
        NewTopic::class
    );
    Notification::assertCount(1);
});

test('create a topic in a subscribed forum does not create a notification for the subscriber when all notifications disabled', function (): void {
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

    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 1,
        'show_subscription_forum' => 1,
    ]);

    $forum = Forum::factory()->create();
    $forumPermission = ForumPermission::factory()->create([
        'group_id'    => $group->id,
        'forum_id'    => $forum->id,
        'start_topic' => true,
        'read_topic'  => true,
    ]);
    $forumSubscription = Subscription::factory()->create([
        'forum_id' => $forum->id,
        'topic_id' => null,
        'user_id'  => $subscriber->id,
    ]);

    $postData = [
        'title'   => 'Sample Topic Title',
        'content' => 'This is the test content.',
    ];

    $response = $this->actingAs($user)->post(route('topics.store', ['id' => $forum->id]), [
        'title'   => $postData['title'],
        'content' => $postData['content'],
    ]);

    $this->assertDatabaseHas('topics', [
        'name'               => $postData['title'],
        'first_post_user_id' => $user->id,
        'forum_id'           => $forum->id,
    ]);

    Notification::assertCount(0);
});

test('create a topic in a subscribed forum does not create a notification for the subscriber when forum subscribe notifications are disabled', function (): void {
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

    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                 => $subscriber->id,
        'block_notifications'     => 0,
        'show_subscription_forum' => 0,
    ]);

    $forum = Forum::factory()->create();
    $forumPermission = ForumPermission::factory()->create([
        'group_id'    => $group->id,
        'forum_id'    => $forum->id,
        'start_topic' => true,
        'read_topic'  => true,
    ]);
    $forumSubscription = Subscription::factory()->create([
        'forum_id' => $forum->id,
        'topic_id' => null,
        'user_id'  => $subscriber->id,
    ]);

    $postData = [
        'title'   => 'Sample Topic Title',
        'content' => 'This is the test content.',
    ];

    $response = $this->actingAs($user)->post(route('topics.store', ['id' => $forum->id]), [
        'title'   => $postData['title'],
        'content' => $postData['content'],
    ]);

    $this->assertDatabaseHas('topics', [
        'name'               => $postData['title'],
        'first_post_user_id' => $user->id,
        'forum_id'           => $forum->id,
    ]);

    Notification::assertCount(0);
});

test('create a topic in a subscribed forum does not create a notification for the subscriber when forum subscribe notifications are disabled for specific group', function (): void {
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

    $user = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $subscriber = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $subscriberNotificationSettings = UserNotification::factory()->create([
        'user_id'                  => $subscriber->id,
        'block_notifications'      => 0,
        'show_subscription_forum'  => 1,
        'json_subscription_groups' => [$group->id],
    ]);

    $forum = Forum::factory()->create();
    $forumPermission = ForumPermission::factory()->create([
        'group_id'    => $group->id,
        'forum_id'    => $forum->id,
        'start_topic' => true,
        'read_topic'  => true,
    ]);
    $forumSubscription = Subscription::factory()->create([
        'forum_id' => $forum->id,
        'topic_id' => null,
        'user_id'  => $subscriber->id,
    ]);

    $postData = [
        'title'   => 'Sample Topic Title',
        'content' => 'This is the test content.',
    ];

    $response = $this->actingAs($user)->post(route('topics.store', ['id' => $forum->id]), [
        'title'   => $postData['title'],
        'content' => $postData['content'],
    ]);

    $this->assertDatabaseHas('topics', [
        'name'               => $postData['title'],
        'first_post_user_id' => $user->id,
        'forum_id'           => $forum->id,
    ]);

    Notification::assertCount(0);
});
