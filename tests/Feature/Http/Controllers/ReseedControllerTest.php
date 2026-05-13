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

use App\Http\Middleware\UpdateLastAction;
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;
use App\Notifications\NewReseedRequest;
use App\Repositories\ChatRepository;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->withoutMiddleware([
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);

    Notification::fake();

    $this->app->instance(ChatRepository::class, new class () extends ChatRepository {
        public function systemMessage(string $message): void
        {
        }
    });
});

test('store creates a reseed request when the requester has leeched long enough', function (): void {
    $torrent = Torrent::factory()->create([
        'seeders' => 0,
    ]);
    $requester = User::factory()->create();
    $potentialReseed = User::factory()->create();

    History::factory()->create([
        'torrent_id' => $torrent->id,
        'user_id'    => $requester->id,
        'active'     => true,
        'seeder'     => false,
        'created_at' => now()->subHours(25),
    ]);
    History::factory()->create([
        'torrent_id' => $torrent->id,
        'user_id'    => $potentialReseed->id,
        'active'     => false,
    ]);

    $this->actingAs($requester)
        ->post(route('reseed', ['id' => $torrent->id]))
        ->assertRedirect(route('torrents.show', ['id' => $torrent->id]))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('torrent_reseeds', [
        'torrent_id'     => $torrent->id,
        'user_id'        => $requester->id,
        'requests_count' => 1,
    ]);
    Notification::assertSentTo($potentialReseed, NewReseedRequest::class);
});

test('store rejects a reseed request before the minimum leech time', function (): void {
    $torrent = Torrent::factory()->create([
        'seeders' => 0,
    ]);
    $requester = User::factory()->create();

    History::factory()->create([
        'torrent_id' => $torrent->id,
        'user_id'    => $requester->id,
        'active'     => true,
        'seeder'     => false,
        'created_at' => now()->subHours(23),
    ]);

    $this->actingAs($requester)
        ->post(route('reseed', ['id' => $torrent->id]))
        ->assertRedirect(route('torrents.show', ['id' => $torrent->id]))
        ->assertSessionHasErrors();

    assertDatabaseCount('torrent_reseeds', 0);
});

test('store rejects a reseed request when the requester is not actively leeching', function (): void {
    $torrent = Torrent::factory()->create([
        'seeders' => 0,
    ]);
    $requester = User::factory()->create();

    $this->actingAs($requester)
        ->post(route('reseed', ['id' => $torrent->id]))
        ->assertRedirect(route('torrents.show', ['id' => $torrent->id]))
        ->assertSessionHasErrors();

    assertDatabaseCount('torrent_reseeds', 0);
});
