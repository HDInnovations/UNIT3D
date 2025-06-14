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
use App\Models\TorrentRequest;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewRequestBounty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('add bounty to request creates a notification for the requester', function (): void {
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
        'can_request' => true,
    ]);

    $requester = User::factory()->create();
    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_bounty' => 1,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'filled_by'     => null,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $bounty = 100;

    $response = $this->actingAs($user)->post(route('requests.bounties.store', ['torrentRequest' => $torrentRequest]), [
        'seedbonus' => $bounty,
        'anon'      => false,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'requests_id' => $torrentRequest->id,
        'seedbonus'   => $bounty,
    ]);

    Notification::assertSentTo(
        [$requester],
        NewRequestBounty::class
    );
    Notification::assertCount(1);
});

test('add bounty to request creates a notification for the requester when bounty notifications not disabled for specific group', function (): void {
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
        'can_request' => true,
    ]);

    $randomGroup = Group::factory()->create();

    $requester = User::factory()->create();
    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_bounty' => 1,
        'json_request_groups' => [$randomGroup->id],
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'filled_by'     => null,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);
    $bounty = 100;

    $response = $this->actingAs($user)->post(route('requests.bounties.store', ['torrentRequest' => $torrentRequest]), [
        'seedbonus' => $bounty,
        'anon'      => false,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'requests_id' => $torrentRequest->id,
        'seedbonus'   => $bounty,
    ]);

    Notification::assertSentTo(
        [$requester],
        NewRequestBounty::class
    );
    Notification::assertCount(1);
});

test('add bounty to request does not create a notification for the requester when all notifications disabled', function (): void {
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
        'can_request' => true,
    ]);

    $requester = User::factory()->create();
    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 1,
        'show_request_bounty' => 1,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'filled_by'     => null,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $bounty = 100;

    $response = $this->actingAs($user)->post(route('requests.bounties.store', ['torrentRequest' => $torrentRequest]), [
        'seedbonus' => $bounty,
        'anon'      => false,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'requests_id' => $torrentRequest->id,
        'seedbonus'   => $bounty,
    ]);

    Notification::assertCount(0);
});

test('add bounty to request does not create a notification for the requester when request bounty notifications are disabled', function (): void {
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
        'can_request' => true,
    ]);

    $requester = User::factory()->create();
    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_bounty' => 0,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'filled_by'     => null,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $bounty = 100;

    $response = $this->actingAs($user)->post(route('requests.bounties.store', ['torrentRequest' => $torrentRequest]), [
        'seedbonus' => $bounty,
        'anon'      => false,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'requests_id' => $torrentRequest->id,
        'seedbonus'   => $bounty,
    ]);

    Notification::assertCount(0);
});

test('add bounty to request does not create a notification for the requester when request bounty notifications are disabled for specific group', function (): void {
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
        'can_request' => true,
    ]);

    $requester = User::factory()->create();
    $user = User::factory()->create([
        'group_id'    => $group->id,
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_bounty' => 1,
        'json_request_groups' => [$group->id],
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'filled_by'     => null,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $bounty = 100;

    $response = $this->actingAs($user)->post(route('requests.bounties.store', ['torrentRequest' => $torrentRequest]), [
        'seedbonus' => $bounty,
        'anon'      => false,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'requests_id' => $torrentRequest->id,
        'seedbonus'   => $bounty,
    ]);

    Notification::assertCount(0);
});
