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

use App\Enums\AuthGuard;
use App\Models\Bot;
use App\Models\Category;
use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Event::fake();
    Queue::fake();

    Bot::factory()->create([
        'command' => 'systembot',
    ]);

    $chatroom = Chatroom::factory()->create();
    config(['chat.system_chatroom' => $chatroom->id]);
});

test('authenticated user can create a torrent request', function (): void {
    $user = User::factory()->create([
        'seedbonus' => 1000.00,
    ]);
    $category = Category::factory()->create([
        'no_meta'    => true,
        'tv_meta'    => false,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
    ]);

    $data = [
        'name'        => 'Test API Torrent Request',
        'description' => 'Detailed description of the requested content',
        'category_id' => $category->id,
        'bounty'      => 200,
        'anon'        => false,
    ];

    $response = $this->actingAs($user, AuthGuard::API->value)
        ->postJson(route('api.requests.store'), $data);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'Test API Torrent Request');

    $this->assertDatabaseHas('requests', [
        'name'        => 'Test API Torrent Request',
        'user_id'     => $user->id,
        'category_id' => $category->id,
    ]);

    $this->assertDatabaseHas('request_bounty', [
        'user_id'   => $user->id,
        'seedbonus' => 200,
    ]);

    expect($user->fresh()->seedbonus)->toEqual(800.00);
});

test('store fails validation when bounty exceeds user seedbonus', function (): void {
    $user = User::factory()->create([
        'seedbonus' => 50.00,
    ]);
    $category = Category::factory()->create([
        'no_meta'    => true,
        'tv_meta'    => false,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
    ]);

    $data = [
        'name'        => 'High Bounty Request',
        'description' => 'Description here',
        'category_id' => $category->id,
        'bounty'      => 500,
        'anon'        => false,
    ];

    $response = $this->actingAs($user, AuthGuard::API->value)
        ->postJson(route('api.requests.store'), $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['bounty']);
});

test('unauthenticated request to store fails with unauthorized status', function (): void {
    $response = $this->postJson(route('api.requests.store'), [
        'name' => 'Unauthorized Request',
    ]);

    $response->assertUnauthorized();
});
