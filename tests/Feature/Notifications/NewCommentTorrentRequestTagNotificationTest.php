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

use App\Http\Livewire\Comments;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\Comment;
use App\Models\Group;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewCommentTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user tags user on request creates a notification for tagged user', function (): void {
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
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                      => $user->id,
        'block_notifications'          => 0,
        'show_mention_request_comment' => 1,
    ]);

    $torrentRequest = TorrentRequest::factory()->create();

    $commentText = '@'.$user->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $torrentRequest])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        $user,
        NewCommentTag::class,
        1,
    );
});

test('user tags user on request creates a notification for tagged user when mentions are disabled for other specific group', function (): void {
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
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                      => $user->id,
        'block_notifications'          => 0,
        'show_mention_request_comment' => 1,
        'json_mention_groups'          => [$randomGroup->id],
    ]);

    $torrentRequest = TorrentRequest::factory()->create();

    $commentText = '@'.$user->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $torrentRequest])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentToTimes(
        $user,
        NewCommentTag::class,
        1,
    );
});

test('user tags user on request does not create a notification for tagged user when all notifications disabled', function (): void {
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
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                      => $user->id,
        'block_notifications'          => 1,
        'show_mention_request_comment' => 1,
    ]);

    $torrentRequest = TorrentRequest::factory()->create();

    $commentText = '@'.$user->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $torrentRequest])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertNotSentTo(
        [$user],
        NewCommentTag::class
    );
});

test('user tags user on request does not create a notification for tagged user when mention notifications are disabled', function (): void {
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
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                      => $user->id,
        'block_notifications'          => 0,
        'show_mention_request_comment' => 0,
    ]);

    $torrentRequest = TorrentRequest::factory()->create();

    $commentText = '@'.$user->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $torrentRequest])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertNotSentTo(
        [$user],
        NewCommentTag::class
    );
});

test('user tags user on request does not create a notification for tagged user when mention notifications are disabled for specific group', function (): void {
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
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'                      => $user->id,
        'block_notifications'          => 0,
        'show_mention_request_comment' => 1,
        'json_mention_groups'          => [$group->id],
    ]);

    $torrentRequest = TorrentRequest::factory()->create();

    $commentText = '@'.$user->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $torrentRequest])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertNotSentTo(
        [$user],
        NewCommentTag::class
    );
});
