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
use App\Models\Category;
use App\Models\Chatroom;
use App\Models\Group;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\NewUpload;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('upload a torrent creates a notification for followers', function (): void {
    $this->markTestIncomplete('Works locally, but fails in pipeline with:
        file_put_contents(/home/runner/work/UNIT3D-Community-Edition/UNIT3D-Community-Edition/files/torrents/676034f8afd077.09743623.torrent): Failed to open stream: No such file or directory');

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
        'is_uploader' => true,
        'can_upload'  => true,
        'is_trusted'  => true,
    ]);

    $follower = User::factory()->create();
    $uploader = User::factory()->create([
        'group_id'   => $group->id,
        'can_upload' => true,
    ]);

    $uploader->followers()->attach($follower->id);

    $this->assertDatabaseHas('follows', [
        'user_id'   => $follower->id,
        'target_id' => $uploader->id,
    ]);

    $followerNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $follower->id,
        'block_notifications'   => 0,
        'show_following_upload' => 1,
    ]);

    $resolution = Resolution::factory()->create([]);
    $category = Category::factory()->create([
        // Prevent upload errors depending on the boolean used in the factory() for meta requirements
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
    ]);
    $type = Type::factory()->create([]);

    $torrent = Torrent::factory()->make();
    $name = 'Pony Music';
    $filePath = base_path('tests/Resources/Pony Music - Mind Fragments (2014).torrent');
    $file = new UploadedFile($filePath, 'Pony Music.torrent', 'text/plain', null, true);

    $response = $this->actingAs($uploader)->post(route('torrents.store'), [
        'torrent'          => $file,
        'name'             => $name,
        'category_id'      => $category->id,
        'type_id'          => $type->id,
        'resolution_id'    => $resolution->id,
        'tmdb_movie_id'    => 111,
        'tmdb_tv_id'       => 0,
        'imdb'             => 111,
        'mal'              => 0,
        'tvdb'             => 0,
        'igdb'             => 0,
        'description'      => 'One song that represents the elements of being lost, abandoned, sadness and innocence.',
        'mediainfo'        => 'Video: Length: 00:00:10',
        'anon'             => $torrent->anon,
        'personal_release' => false,
    ]);

    $response = $this->followRedirects($response);
    $response->assertOk();
    $response->assertViewIs('torrent.download-check');

    Notification::assertSentTo(
        [$follower],
        NewUpload::class
    );
    Notification::assertCount(1);
});

test('upload a torrent does not create a notification for followers when all notifications disabled', function (): void {
    $this->markTestIncomplete('Works locally, but fails in pipeline with:
        file_put_contents(/home/runner/work/UNIT3D-Community-Edition/UNIT3D-Community-Edition/files/torrents/676034f8afd077.09743623.torrent): Failed to open stream: No such file or directory');

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
        'is_uploader' => true,
        'can_upload'  => true,
        'is_trusted'  => true,
    ]);

    $follower = User::factory()->create();
    $uploader = User::factory()->create([
        'group_id'   => $group->id,
        'can_upload' => true,
    ]);

    $uploader->followers()->attach($follower->id);

    $this->assertDatabaseHas('follows', [
        'user_id'   => $follower->id,
        'target_id' => $uploader->id,
    ]);

    $followerNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $follower->id,
        'block_notifications'   => 1,
        'show_following_upload' => 1,
    ]);

    $resolution = Resolution::factory()->create([]);
    $category = Category::factory()->create([
        // Prevent upload errors depending on the boolean used in the factory() for meta requirements
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
    ]);
    $type = Type::factory()->create([]);

    $torrent = Torrent::factory()->make();
    $name = 'Pony Music';
    $filePath = base_path('tests/Resources/Pony Music - Mind Fragments (2014).torrent');
    $file = new UploadedFile($filePath, 'Pony Music.torrent', 'text/plain', null, true);

    $response = $this->actingAs($uploader)->post(route('torrents.store'), [
        'torrent'          => $file,
        'name'             => $name,
        'category_id'      => $category->id,
        'type_id'          => $type->id,
        'resolution_id'    => $resolution->id,
        'tmdb_movie_id'    => 111,
        'tmdb_tv_id'       => 0,
        'imdb'             => 111,
        'mal'              => 0,
        'tvdb'             => 0,
        'igdb'             => 0,
        'description'      => 'One song that represents the elements of being lost, abandoned, sadness and innocence.',
        'mediainfo'        => 'Video: Length: 00:00:10',
        'anon'             => $torrent->anon,
        'personal_release' => false,
    ]);

    $response = $this->followRedirects($response);
    $response->assertOk();
    $response->assertViewIs('torrent.download-check');

    Notification::assertCount(0);
});

test('upload a torrent does not create a notification for followers when following upload notifications are disabled', function (): void {
    $this->markTestIncomplete('Works locally, but fails in pipeline with:
        file_put_contents(/home/runner/work/UNIT3D-Community-Edition/UNIT3D-Community-Edition/files/torrents/676034f8afd077.09743623.torrent): Failed to open stream: No such file or directory');

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
        'is_uploader' => true,
        'can_upload'  => true,
        'is_trusted'  => true,
    ]);

    $follower = User::factory()->create();
    $uploader = User::factory()->create([
        'group_id'   => $group->id,
        'can_upload' => true,
    ]);

    $uploader->followers()->attach($follower->id);

    $this->assertDatabaseHas('follows', [
        'user_id'   => $follower->id,
        'target_id' => $uploader->id,
    ]);

    $followerNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $follower->id,
        'block_notifications'   => 0,
        'show_following_upload' => 0,
    ]);

    $resolution = Resolution::factory()->create([]);
    $category = Category::factory()->create([
        // Prevent upload errors depending on the boolean used in the factory() for meta requirements
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
    ]);
    $type = Type::factory()->create([]);

    $torrent = Torrent::factory()->make();
    $name = 'Pony Music';
    $filePath = base_path('tests/Resources/Pony Music - Mind Fragments (2014).torrent');
    $file = new UploadedFile($filePath, 'Pony Music.torrent', 'text/plain', null, true);

    $response = $this->actingAs($uploader)->post(route('torrents.store'), [
        'torrent'          => $file,
        'name'             => $name,
        'category_id'      => $category->id,
        'type_id'          => $type->id,
        'resolution_id'    => $resolution->id,
        'tmdb_movie_id'    => 111,
        'tmdb_tv_id'       => 0,
        'imdb'             => 111,
        'mal'              => 0,
        'tvdb'             => 0,
        'igdb'             => 0,
        'description'      => 'One song that represents the elements of being lost, abandoned, sadness and innocence.',
        'mediainfo'        => 'Video: Length: 00:00:10',
        'anon'             => $torrent->anon,
        'personal_release' => false,
    ]);

    $response = $this->followRedirects($response);
    $response->assertOk();
    $response->assertViewIs('torrent.download-check');

    Notification::assertCount(0);
});

test('upload a torrent does not create a notification for followers when following upload notifications are disabled for specific group', function (): void {
    $this->markTestIncomplete('Works locally, but fails in pipeline with:
        file_put_contents(/home/runner/work/UNIT3D-Community-Edition/UNIT3D-Community-Edition/files/torrents/676034f8afd077.09743623.torrent): Failed to open stream: No such file or directory');

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
        'is_uploader' => true,
        'can_upload'  => true,
        'is_trusted'  => true,
    ]);

    $follower = User::factory()->create();
    $uploader = User::factory()->create([
        'group_id'   => $group->id,
        'can_upload' => true,
    ]);

    $uploader->followers()->attach($follower->id);

    $this->assertDatabaseHas('follows', [
        'user_id'   => $follower->id,
        'target_id' => $uploader->id,
    ]);

    $followerNotificationSettings = UserNotification::factory()->create([
        'user_id'               => $follower->id,
        'block_notifications'   => 0,
        'show_following_upload' => 1,
        'json_following_groups' => [$group->id],
    ]);

    $resolution = Resolution::factory()->create([]);
    $category = Category::factory()->create([
        // Prevent upload errors depending on the boolean used in the factory() for meta requirements
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
    ]);
    $type = Type::factory()->create([]);

    $torrent = Torrent::factory()->make();
    $name = 'Pony Music';
    $filePath = base_path('tests/Resources/Pony Music - Mind Fragments (2014).torrent');
    $file = new UploadedFile($filePath, 'Pony Music.torrent', 'text/plain', null, true);

    $response = $this->actingAs($uploader)->post(route('torrents.store'), [
        'torrent'          => $file,
        'name'             => $name,
        'category_id'      => $category->id,
        'type_id'          => $type->id,
        'resolution_id'    => $resolution->id,
        'tmdb_movie_id'    => 111,
        'tmdb_tv_id'       => 0,
        'imdb'             => 111,
        'mal'              => 0,
        'tvdb'             => 0,
        'igdb'             => 0,
        'description'      => 'One song that represents the elements of being lost, abandoned, sadness and innocence.',
        'mediainfo'        => 'Video: Length: 00:00:10',
        'anon'             => $torrent->anon,
        'personal_release' => false,
    ]);

    $response = $this->followRedirects($response);
    $response->assertOk();
    $response->assertViewIs('torrent.download-check');

    Notification::assertCount(0);
});
