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
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewBon;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('gift a user creates a notification for the gifted user', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $sender = User::factory()->create([
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 0,
        'show_bon_gift'       => 1,
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertSentTo(
        [$receiver],
        NewBon::class
    );
    Notification::assertCount(1);
});

test('gift a user creates a notification for the gifted user when gift notifications are not disabled for specific group', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $group = Group::factory()->create();
    $randomGroup = Group::factory()->create();

    $sender = User::factory()->create([
        'group_id'  => $group->id,
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 0,
        'show_bon_gift'       => 1,
        'json_bon_groups'     => [$randomGroup->id],
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertSentTo(
        [$receiver],
        NewBon::class
    );
    Notification::assertCount(1);
});

test('staff gifts a user creates a notification for the gifted user even when gift notifications are disabled', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);

    $sender = User::factory()->create([
        'group_id'  => $group->id,
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 0,
        'show_bon_gift'       => 0,
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertSentTo(
        [$receiver],
        NewBon::class
    );
    Notification::assertCount(1);
});

test('gift a user does not create a notification for the gifted user when all notifications disabled', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $sender = User::factory()->create([
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 1,
        'show_bon_gift'       => 1,
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertCount(0);
});

test('gift a user does not create a notification for the gifted user when gift notifications are disabled', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $sender = User::factory()->create([
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create();

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 0,
        'show_bon_gift'       => 0,
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertCount(0);
});

test('gift a user does not create a notification for the gifted user when gift notifications are disabled for specific group', function (): void {
    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    Notification::fake();

    $group = Group::factory()->create();

    $sender = User::factory()->create([
        'group_id'  => $group->id,
        'seedbonus' => 1000,
    ]);
    $receiver = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $userNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $receiver->id,
        'block_notifications' => 0,
        'show_bon_gift'       => 1,
        'json_bon_groups'     => [$group->id],
    ]);

    $response = $this->actingAs($sender)->post(route('users.gifts.store', ['user' => $receiver]), [
        'sender_id'          => $sender->id,
        'recipient_username' => $receiver->username,
        'message'            => 'foo',
        'bon'                => 100.00,
    ]);

    // The transaction must complete to trigger a notification
    $this->assertDatabaseHas('gifts', [
        'bon'          => '100.00',
        'sender_id'    => $sender->id,
        'recipient_id' => $receiver->id,
    ]);

    Notification::assertCount(0);
});
