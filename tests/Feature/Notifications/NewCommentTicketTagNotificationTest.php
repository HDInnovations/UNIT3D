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
use App\Notifications\NewCommentTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user tags user on ticket creates a notification for tagged user', function (): void {
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
        'is_admin' => true,
    ]);

    $staff = User::factory()->create([
        'username' => 'TestUsername',
        'group_id' => $staffGroup->id,
    ]);
    $commenter = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $staffNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $staff->id,
        'block_notifications' => 0,
    ]);

    $ticket = Ticket::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $commentText = '@'.$staff->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertSentTo(
        [$staff],
        NewCommentTag::class
    );
    // Tag notification for user and notification for staff ticket poster gets sent
    Notification::assertCount(3);
});

test('user tags user on ticket does not create a notification for tagged user when all notifications disabled', function (): void {
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
        'is_admin' => true,
    ]);

    $staff = User::factory()->create([
        'username' => 'TestUsername',
        'group_id' => $staffGroup->id,
    ]);
    $commenter = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $staffNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $staff->id,
        'block_notifications' => 1,
    ]);

    $ticket = Ticket::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $commentText = '@'.$staff->username.' Test';

    Livewire::actingAs($commenter)
        ->test(Comments::class, ['model' => $ticket])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertNotSentTo(
        [$staff],
        NewCommentTag::class
    );
});
