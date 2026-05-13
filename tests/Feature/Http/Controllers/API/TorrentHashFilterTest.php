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
use App\Models\Category;
use App\Models\Group;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

beforeEach(function (): void {
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);
});

test('torrent API filter can search by info hash', function (): void {
    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);
    $category = Category::factory()->create([
        'no_meta'    => false,
        'music_meta' => false,
        'game_meta'  => false,
        'tv_meta'    => false,
        'movie_meta' => false,
    ]);
    $matchingTorrent = Torrent::factory()->create([
        'category_id' => $category->id,
        'info_hash'   => '12345678901234567890',
    ]);
    Torrent::factory()->create([
        'category_id' => $category->id,
        'info_hash'   => 'abcdefghijklmnopqrst',
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson(
        'api/torrents/filter?driver=sql&hash='.bin2hex($matchingTorrent->info_hash)
    );

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string) $matchingTorrent->id);
});

test('torrent API filter validates info hash format', function (): void {
    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson(
        'api/torrents/filter?driver=sql&hash=not-a-valid-info-hash'
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['hash']);
});
