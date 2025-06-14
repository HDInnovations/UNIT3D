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
use App\Models\Article;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\Comment;
use App\Models\Group;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user comments on article creates a notification for staff', function (): void {
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

    $staffNotificationSettings = UserNotification::factory()->for($staff)->create([
        'block_notifications' => 0,
    ]);

    $article = Article::factory()->for($staff)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($user)
        ->test(Comments::class, ['model' => $article])
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

test('staff comments on own article does not create a notification for staff', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $staffGroup = Group::factory()->create([
        'can_comment' => true,
        'is_modo'     => true,
    ]);

    $staff = User::factory()->create([
        'group_id'    => $staffGroup->id,
        'can_comment' => true,
    ]);

    $staffNotificationSettings = UserNotification::factory()->for($staff)->create([
        'block_notifications' => 0,
    ]);

    $article = Article::factory()->for($staff)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($staff)
        ->test(Comments::class, ['model' => $article])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertCount(0);
});

test('user comments on article does not a notification for staff user when all notifications are disabled', function (): void {
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

    $staffNotificationSettings = UserNotification::factory()->for($staff)->create([
        'block_notifications' => 1,
    ]);

    $article = Article::factory()->for($staff)->create();

    $commentText = 'This is a test comment';

    Livewire::actingAs($user)
        ->test(Comments::class, ['model' => $article])
        ->set('newCommentState', $commentText)
        ->set('anon', false)
        ->call('postComment');

    $this->assertEquals(1, Comment::count());

    Notification::assertCount(0);
});
