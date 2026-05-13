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
use App\Http\Middleware\UpdateLastAction;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Type;
use App\Models\User;
use App\Repositories\ChatRepository;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->withoutMiddleware([
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);

    $this->app->instance(ChatRepository::class, new class () extends ChatRepository {
        public function systemMessage(string $message): void
        {
        }
    });
});

test('store creates a torrent request from the api', function (): void {
    $category = Category::factory()->create([
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => true,
    ]);
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();
    $user = User::factory()->create([
        'can_request' => true,
        'seedbonus'   => 1000,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)
        ->postJson('/api/requests', [
            'name'          => 'API Wanted',
            'description'   => 'Created from the API.',
            'category_id'   => $category->id,
            'type_id'       => $type->id,
            'resolution_id' => $resolution->id,
            'bounty'        => 250,
            'anonymous'     => false,
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', 'API Wanted')
        ->assertJsonPath('data.description', 'Created from the API.')
        ->assertJsonPath('data.category_id', $category->id)
        ->assertJsonPath('data.user', $user->username);

    assertDatabaseHas('requests', [
        'name'          => 'API Wanted',
        'description'   => 'Created from the API.',
        'category_id'   => $category->id,
        'type_id'       => $type->id,
        'resolution_id' => $resolution->id,
        'user_id'       => $user->id,
        'bounty'        => 250,
        'anon'          => false,
    ]);
    assertDatabaseHas('request_bounty', [
        'user_id'   => $user->id,
        'seedbonus' => 250,
        'anon'      => false,
    ]);
    assertDatabaseHas('users', [
        'id'        => $user->id,
        'seedbonus' => 750,
    ]);
});

test('store rejects a torrent request when bounty exceeds user balance', function (): void {
    $category = Category::factory()->create([
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => true,
    ]);
    $user = User::factory()->create([
        'can_request' => true,
        'seedbonus'   => 50,
    ]);

    $this->actingAs($user, AuthGuard::API->value)
        ->postJson('/api/requests', [
            'name'        => 'Too Expensive',
            'description' => 'The bounty is higher than the user balance.',
            'category_id' => $category->id,
            'bounty'      => 100,
            'anon'        => false,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('bounty');

    assertDatabaseCount('requests', 0);
    assertDatabaseCount('request_bounty', 0);
});
