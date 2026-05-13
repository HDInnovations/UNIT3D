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

test('torrent API shows global freeleech as effective freeleech', function (): void {
    config(['other.freeleech' => true]);

    $user = User::factory()->create();
    $torrent = Torrent::factory()->create([
        'category_id' => Category::factory()->create([
            'no_meta'    => false,
            'music_meta' => false,
            'game_meta'  => false,
            'tv_meta'    => false,
            'movie_meta' => false,
        ])->id,
        'free' => 0,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson('api/torrents/'.$torrent->id);

    $response->assertOk()
        ->assertJsonPath('attributes.freeleech', '100%');
});

test('torrent API shows group freeleech as effective freeleech', function (): void {
    config(['other.freeleech' => false]);

    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_freeleech' => true])->id,
    ]);
    $torrent = Torrent::factory()->create([
        'category_id' => Category::factory()->create([
            'no_meta'    => false,
            'music_meta' => false,
            'game_meta'  => false,
            'tv_meta'    => false,
            'movie_meta' => false,
        ])->id,
        'free' => 0,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson('api/torrents/'.$torrent->id);

    $response->assertOk()
        ->assertJsonPath('attributes.freeleech', '100%');
});

test('torrent API keeps torrent freeleech percentage without global or group freeleech', function (): void {
    config(['other.freeleech' => false]);

    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_freeleech' => false])->id,
    ]);
    $torrent = Torrent::factory()->create([
        'category_id' => Category::factory()->create([
            'no_meta'    => false,
            'music_meta' => false,
            'game_meta'  => false,
            'tv_meta'    => false,
            'movie_meta' => false,
        ])->id,
        'free' => 50,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson('api/torrents/'.$torrent->id);

    $response->assertOk()
        ->assertJsonPath('attributes.freeleech', '50%');
});
