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
use App\Models\Category;
use App\Models\Group;
use App\Models\Resolution;
use App\Models\TorrentRequest;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewRequestFill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('fill a request creates a notification for the request creator', function (): void {
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

    $requester = User::factory()->create();
    $filler = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_fill'   => 1,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'filled_by'     => null,
        'torrent_id'    => null,
        'claimed'       => false,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $response = $this->actingAs($filler)->post(route('requests.fills.store', [$torrentRequest]), [
        'torrent_id'  => $torrent->id,
        'filled_anon' => 0,
    ]);

    Notification::assertSentTo(
        [$requester],
        NewRequestFill::class
    );
    Notification::assertCount(1);
});

test('fill a request creates a notification for the request creator when request-fill notifications are not disabled for specific group', function (): void {
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

    $requester = User::factory()->create();
    $filler = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_fill'   => 1,
        'json_request_groups' => [$randomGroup->id],
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'filled_by'     => null,
        'torrent_id'    => null,
        'claimed'       => false,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $response = $this->actingAs($filler)->post(route('requests.fills.store', [$torrentRequest]), [
        'torrent_id'  => $torrent->id,
        'filled_anon' => 0,
    ]);

    Notification::assertSentTo(
        [$requester],
        NewRequestFill::class
    );
    Notification::assertCount(1);
});

test('fill a request does not create a notification for the request creator when all notifications are disabled', function (): void {
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

    $requester = User::factory()->create();
    $filler = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 1,
        'show_request_fill'   => 1,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'filled_by'     => null,
        'torrent_id'    => null,
        'claimed'       => false,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $response = $this->actingAs($filler)->post(route('requests.fills.store', [$torrentRequest]), [
        'torrent_id'  => $torrent->id,
        'filled_anon' => 0,
    ]);

    Notification::assertCount(0);
});

test('fill a request does not create a notification for the request creator when request notifications are disabled', function (): void {
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

    $requester = User::factory()->create();
    $filler = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_fill'   => 0,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'filled_by'     => null,
        'torrent_id'    => null,
        'claimed'       => false,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $response = $this->actingAs($filler)->post(route('requests.fills.store', [$torrentRequest]), [
        'torrent_id'  => $torrent->id,
        'filled_anon' => 0,
    ]);

    Notification::assertCount(0);
});

test('fill a request does not create a notification for the request creator when request notifications are disabled for specific group', function (): void {
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

    $requester = User::factory()->create();
    $filler = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $requesterNotificationSettings = UserNotification::factory()->create([
        'user_id'             => $requester->id,
        'block_notifications' => 0,
        'show_request_fill'   => 1,
        'json_request_groups' => [$group->id],
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrentRequest = TorrentRequest::factory()->create([
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'filled_by'     => null,
        'torrent_id'    => null,
        'claimed'       => false,
        'filled_when'   => null,
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $response = $this->actingAs($filler)->post(route('requests.fills.store', [$torrentRequest]), [
        'torrent_id'  => $torrent->id,
        'filled_anon' => 0,
    ]);

    Notification::assertCount(0);
});
