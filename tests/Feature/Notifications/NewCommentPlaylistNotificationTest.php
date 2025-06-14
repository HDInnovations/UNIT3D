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
use App\Models\Playlist;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('comments on playlist creates a notification for playlist owner', function (): void {
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

    $owner = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $ownerNotificationSettings = UserNotification::factory()->for($owner)->create([
        'block_notifications' => 0,
    ]);

    $playlist = Playlist::factory()->for($owner)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $playlist])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        [$owner],
        NewComment::class
    );
    Notification::assertCount(1);
});

test('user comments on own playlist does not create a notification for self', function (): void {
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

    $owner = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $ownerNotificationSettings = UserNotification::factory()->for($owner)->create([
        'block_notifications' => 0,
    ]);

    $playlist = Playlist::factory()->for($owner)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($owner)
        ->test(Comments::class, ['model' => $playlist])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertCount(0);
});

test('comments on playlist does not create a notification for playlist owner when all notifications are disabled', function (): void {
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

    $owner = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $commenter = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);

    $ownerNotificationSettings = UserNotification::factory()->for($owner)->create([
        'block_notifications' => 1,
    ]);

    $playlist = Playlist::factory()->for($owner)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $playlist])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertCount(0);
});
