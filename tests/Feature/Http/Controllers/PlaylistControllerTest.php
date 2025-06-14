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

use App\Http\Controllers\PlaylistController;
use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Models\Playlist;
use App\Models\PlaylistCategory;
use App\Models\User;
use Database\Seeders\BotSeeder;
use Database\Seeders\ChatroomSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;

test('create returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('playlists.create'));
    $response->assertOk();
    $response->assertViewIs('playlist.create');
});

test('destroy returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);

    $response = $this->actingAs($user)->delete(route('playlists.destroy', [$playlist]));
    $response->assertRedirect(route('playlists.index'))->assertSessionHas('success', trans('playlist.deleted'));

    $this->assertDatabaseMissing('playlists', [
        'id' => $playlist->id,
    ]);
});

test('destroy aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => User::factory()->create()->id, // create a playlist with a different user
    ]);

    $response = $this->actingAs($user)->delete(route('playlists.destroy', [$playlist]));
    $response->assertForbidden();
});

test('edit returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);

    $response = $this->actingAs($user)->get(route('playlists.edit', [$playlist]));
    $response->assertOk();
    $response->assertViewIs('playlist.edit');
    $response->assertViewHas('playlist', $playlist);
});

test('edit aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => User::factory()->create()->id, // create a playlist with a different user
    ]);

    $response = $this->actingAs($user)->get(route('playlists.edit', [$playlist]));
    $response->assertForbidden();
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('playlists.index'));
    $response->assertOk();
    $response->assertViewIs('playlist.index');
});

test('show returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);

    $response = $this->actingAs($user)->get(route('playlists.show', [$playlist]));
    $response->assertOk();
    $response->assertViewIs('playlist.show');
    $response->assertViewHas('playlist', $playlist);
    $response->assertViewHas('meta');
    $response->assertViewHas('torrents');
});

test('show aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id'    => User::factory()->create()->id, // create a playlist with a different user
        'is_private' => true, // make the playlist private
    ]);

    $response = $this->actingAs($user)->get(route('playlists.show', [$playlist]));
    $response->assertForbidden();
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        PlaylistController::class,
        'store',
        StorePlaylistRequest::class
    );
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(ChatroomSeeder::class);
    $this->seed(BotSeeder::class);

    $user = User::factory()->create();
    $playlist = Playlist::factory()->make();
    $playlistCategory = PlaylistCategory::factory()->create();

    $file = UploadedFile::fake()->image('playlist-cover.png');

    $response = $this->actingAs($user)->post(route('playlists.store'), [
        'name'                 => $playlist->name,
        'playlist_category_id' => $playlistCategory->id,
        'description'          => $playlist->description,
        'cover_image'          => $file,
        'is_private'           => false,
        'is_pinned'            => $playlist->is_pinned,
        'is_featured'          => $playlist->is_featured,
        'user_id'              => $user->id,
    ]);
    $response->assertSessionHas('success', trans('playlist.published-success'));

    $this->assertDatabaseHas('playlists', [
        'name' => $playlist->name,
    ]);
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        PlaylistController::class,
        'update',
        UpdatePlaylistRequest::class
    );
});

test('update returns an ok response', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id' => $user->id, // create a playlist with the same user
    ]);
    $playlistCategory = PlaylistCategory::factory()->create();

    $file = UploadedFile::fake()->image('playlist-cover.png');

    $response = $this->actingAs($user)->patch(route('playlists.update', [$playlist]), [
        'name'                 => 'Test Playlist Name Updated',
        'playlist_category_id' => $playlistCategory->id,
        'description'          => 'Test Playlist Description Updated',
        'cover_image'          => $file,
        'is_private'           => true,
    ]);
    $response->assertRedirect(route('playlists.show', ['playlist' => $playlist]))->assertSessionHas('success', trans('playlist.update-success'));

    $this->assertDatabaseHas('playlists', [
        'name' => 'Test Playlist Name Updated',
    ]);
});

test('update aborts with a 403', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create([
        'user_id'    => User::factory()->create()->id, // create a playlist with a different user
        'is_private' => true, // make the playlist private
    ]);
    $playlistCategory = PlaylistCategory::factory()->create();

    $response = $this->actingAs($user)->patch(route('playlists.update', [$playlist]), [
        'name'                 => 'Test Playlist Name Updated',
        'playlist_category_id' => $playlistCategory->id,
        'description'          => 'Test Playlist Description Updated',
        'is_private'           => false,
    ]);
    $response->assertForbidden();
});
