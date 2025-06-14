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
use App\Notifications\NewRequestFillApprove;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('accept a request fill creates a notification for the filler', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $requester = User::factory()->create();
    $filler = User::factory()->create();

    $fillerNotificationSettings = UserNotification::factory()->create([
        'user_id'                   => $filler->id,
        'block_notifications'       => 0,
        'show_request_fill_approve' => 1,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'anon'          => false,
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'torrent_id'    => $torrent->id,
        'claimed'       => true,
        'filled_by'     => $filler->id,
        'filled_when'   => now(),
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $response = $this->actingAs($requester)->post(route('requests.approved_fills.store', [$torrentRequest]));

    $response->assertRedirect(route('requests.show', $torrentRequest));

    Notification::assertSentTo(
        [$filler],
        NewRequestFillApprove::class
    );
    Notification::assertCount(1);
});

test('accept a request fill creates a notification for the filler when request accept notifications are not disabled for specific group', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $randomGroup = Group::factory()->create();

    $requester = User::factory()->create();
    $filler = User::factory()->create();

    $fillerNotificationSettings = UserNotification::factory()->create([
        'user_id'                   => $filler->id,
        'block_notifications'       => 0,
        'show_request_fill_approve' => 1,
        'json_request_groups'       => [$randomGroup->id],
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'anon'          => false,
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'torrent_id'    => $torrent->id,
        'claimed'       => true,
        'filled_by'     => $filler->id,
        'filled_when'   => now(),
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $response = $this->actingAs($requester)->post(route('requests.approved_fills.store', [$torrentRequest]));

    $response->assertRedirect(route('requests.show', $torrentRequest));

    Notification::assertSentTo(
        [$filler],
        NewRequestFillApprove::class
    );
    Notification::assertCount(1);
});

test('accept a request fill creates a notification for the filler when all notifications are disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $randomGroup = Group::factory()->create();

    $requester = User::factory()->create();
    $filler = User::factory()->create();

    $fillerNotificationSettings = UserNotification::factory()->create([
        'user_id'                   => $filler->id,
        'block_notifications'       => 1,
        'show_request_fill_approve' => 1,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'anon'          => false,
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'torrent_id'    => $torrent->id,
        'claimed'       => true,
        'filled_by'     => $filler->id,
        'filled_when'   => now(),
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $response = $this->actingAs($requester)->post(route('requests.approved_fills.store', [$torrentRequest]));

    $response->assertRedirect(route('requests.show', $torrentRequest));

    Notification::assertCount(0);
});

test('accept a request fill creates a notification for the filler when fill approve notifications are disabled', function (): void {
    Notification::fake();

    // Required for ChatRepository()
    $this->seed(UserSeeder::class);

    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chat = Chatroom::factory()->create([
        'name' => config('chat.system_chatroom'),
    ]);

    $requester = User::factory()->create();
    $filler = User::factory()->create();

    $fillerNotificationSettings = UserNotification::factory()->create([
        'user_id'                   => $filler->id,
        'block_notifications'       => 0,
        'show_request_fill_approve' => 0,
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'anon'          => false,
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'torrent_id'    => $torrent->id,
        'claimed'       => true,
        'filled_by'     => $filler->id,
        'filled_when'   => now(),
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $response = $this->actingAs($requester)->post(route('requests.approved_fills.store', [$torrentRequest]));

    $response->assertRedirect(route('requests.show', $torrentRequest));

    Notification::assertCount(0);
});

test('accept a request fill creates a notification for the filler when request notifications are disabled for specific group', function (): void {
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

    $requester = User::factory()->create([
        'group_id' => $group->id,
    ]);
    $filler = User::factory()->create();

    $fillerNotificationSettings = UserNotification::factory()->create([
        'user_id'                   => $filler->id,
        'block_notifications'       => 0,
        'show_request_fill_approve' => 1,
        'json_request_groups'       => [$group->id],
    ]);

    $category = Category::factory()->create();
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
    ]);

    $torrentRequest = TorrentRequest::factory()->create([
        'anon'          => false,
        'user_id'       => $requester->id,
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'torrent_id'    => $torrent->id,
        'claimed'       => true,
        'filled_by'     => $filler->id,
        'filled_when'   => now(),
        'approved_by'   => null,
        'approved_when' => null,
    ]);

    $response = $this->actingAs($requester)->post(route('requests.approved_fills.store', [$torrentRequest]));

    $response->assertRedirect(route('requests.show', $torrentRequest));

    Notification::assertCount(0);
});
