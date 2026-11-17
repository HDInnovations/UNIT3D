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

use App\Models\Torrent;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Support\Facades\Http;

test('destroy returns an ok response', function (): void {
    $user = User::factory()->create();
    $wish = Wish::factory()->for($user)->create();

    $this->actingAs($user)->delete(route('users.wishes.destroy', [$user, $wish]))
        ->assertRedirectToRoute('users.wishes.index', [$user])
        ->assertSessionHas('success', 'Wish successfully removed!');

    $this->assertModelMissing($wish);
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();
    $wishes = Wish::factory()->for($user)->times(3)->create();

    $this->actingAs($user)->get(route('users.wishes.index', [$user]))
        ->assertOk()
        ->assertViewIs('user.wish.index')
        ->assertViewHas('user', $user)
        ->assertViewHas('wishes')
        ->assertViewHas('movieCategoryIds')
        ->assertViewHas('tvCategoryIds')
        ->assertViewHas('route');
});

test('store movie returns an ok response', function (): void {
    $user = User::factory()->create();
    $torrent = Torrent::factory()->create();
    $wish = Wish::factory()->for($user)->make();

    Http::fake([
        // cspell:ignore themoviedb
        'api.themoviedb.org/3/movie/*' => Http::response([
            'title'        => 'Test',
            'release_date' => '2000-01-01',
        ]),
    ]);

    $response = $this->actingAs($user)->post(route('users.wishes.store', [$user]), [
        'meta'          => 'movie',
        'tmdb_movie_id' => $wish->tmdb_movie_id,
    ])
        ->assertRedirectToRoute('users.wishes.index', [$user])
        ->assertSessionHas('success', 'Wish successfully added!');

    $this->assertDatabaseHas('wishes', [
        'user_id'       => $user->id,
        'tmdb_movie_id' => $wish->tmdb_movie_id,
    ]);
});
