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
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user comments own ticket does not create a notification for self but assigned staff', function (): void {
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
    $staffGroup = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $staff = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->for($user)->create([
        'block_notifications' => 0,
    ]);
    $staffNotificationSettings = UserNotification::factory()->for($staff)->create([
        'block_notifications' => 0,
    ]);

    $ticket = Ticket::factory()->create([
        'user_id'  => $user->id,
        'staff_id' => $staff->id,
    ]);

    $commentText = 'This is a test comment';

    Livewire::actingAs($user)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        [$staff],
        NewComment::class
    );
    Notification::assertCount(1);
});

test('user comments own ticket does not create a notification for staff when none is assigned', function (): void {
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
    $staffGroup = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $staff = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->for($user)->create([
        'block_notifications' => 0,
    ]);

    $ticket = Ticket::factory()->for($user)->create([
        'staff_id' => null,
    ]);

    $commentText = 'This is a test comment';

    Livewire::actingAs($user)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertCount(0);
});

test('staff comments a ticket creates a notification for the user but not staff', function (): void {
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
    $staffGroup = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
    ]);
    $staff = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->for($user)->create([
        'block_notifications' => 0,
    ]);

    $ticket = Ticket::factory()->for($user)->create([
        'staff_id' => $staff->id,
    ]);

    $commentText = 'This is a test comment';

    Livewire::actingAs($staff)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        [$user],
        NewComment::class
    );
    Notification::assertCount(1);
});

test('staff comments a ticket create a notification for the user even when all notifications are disabled', function (): void {
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
        'can_request' => true,
    ]);
    $staffGroup = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_comment' => true,
        'can_request' => true,
    ]);
    $staff = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $userNotificationSettings = UserNotification::factory()->for($user)->create([
        'block_notifications' => 1,
    ]);

    $ticket = Ticket::factory()->for($user)->create([
        'staff_id' => $staff->id,
    ]);

    $commentText = 'This is a test comment';

    Livewire::actingAs($staff)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        [$user],
        NewComment::class
    );
    Notification::assertCount(1);
});
