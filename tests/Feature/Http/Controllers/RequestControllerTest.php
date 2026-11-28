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

use App\Models\Category;
use App\Models\Resolution;
use App\Models\TorrentRequest;
use App\Models\Type;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Http;

test('create returns an ok response', function (): void {
    $user = User::factory()->create();
    Category::factory()->times(3)->create();
    Type::factory()->times(3)->create();
    Resolution::factory()->times(3)->create();

    $this->actingAs($user)->get(route('requests.create'))
        ->assertOk()
        ->assertViewIs('requests.create')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('user', $user)
        ->assertViewHas('category_id')
        ->assertViewHas('title')
        ->assertViewHas('imdb')
        ->assertViewHas('movieId')
        ->assertViewHas('tvId')
        ->assertViewHas('mal')
        ->assertViewHas('tvdb')
        ->assertViewHas('igdb');
});

test('destroy returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->moderator()->create();

    $this->actingAs($user)->delete(route('requests.destroy', [$torrentRequest]))
        ->assertRedirectToRoute('requests.index')
        ->assertSessionHas('success');

    $this->assertModelMissing($torrentRequest);
});

test('edit returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->create();
    Category::factory()->times(3)->create();
    Type::factory()->times(3)->create();
    Resolution::factory()->times(3)->create();

    $this->actingAs($user)->get(route('requests.edit', [$torrentRequest]))
        ->assertOk()
        ->assertViewIs('requests.edit')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('user')
        ->assertViewHas('torrentRequest');
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();
    TorrentRequest::factory()->times(3)->create();

    $this->actingAs($user)->get(route('requests.index'))
        ->assertOk()
        ->assertViewIs('requests.index');
});

test('show returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->hasMovie(1)->create();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('requests.show', [$torrentRequest]))
        ->assertOk()
        ->assertViewIs('requests.show')
        ->assertViewHas('torrentRequest', $torrentRequest)
        ->assertViewHas('user', $user)
        ->assertViewHas('canEdit');
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->create(['seedbonus' => 100000]);
    $category = Category::factory()->create(['tv_meta' => true]);
    $torrentRequest = TorrentRequest::factory()
        ->for($user)
        ->for($category)
        ->make(['bounty' => 1000]);

    Http::fake();

    $this->actingAs($user)
        ->post(route('requests.store'), [
            'name'                 => $torrentRequest->name,
            'title_exists_on_imdb' => true,
            'imdb'                 => $torrentRequest->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $torrentRequest->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $torrentRequest->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $torrentRequest->mal,
            'season_number'        => $torrentRequest->season_number,
            'episode_number'       => $torrentRequest->episode_number,
            'category_id'          => $torrentRequest->category_id,
            'type_id'              => $torrentRequest->type_id,
            'resolution_id'        => $torrentRequest->resolution_id,
            'description'          => $torrentRequest->description,
            'bounty'               => 1000,
            'anon'                 => $torrentRequest->anon,
        ])
        ->assertRedirectToRoute('requests.index')
        ->assertSessionHas('success', __('request.added-request'));

    $this->assertDatabaseHas('requests', [
        'name'           => $torrentRequest->name,
        'imdb'           => $torrentRequest->imdb,
        'tvdb'           => $torrentRequest->tvdb,
        'tmdb_tv_id'     => $torrentRequest->tmdb_tv_id,
        'mal'            => $torrentRequest->mal,
        'season_number'  => $torrentRequest->season_number,
        'episode_number' => $torrentRequest->episode_number,
        'category_id'    => $torrentRequest->category_id,
        'type_id'        => $torrentRequest->type_id,
        'resolution_id'  => $torrentRequest->resolution_id,
        'description'    => $torrentRequest->description,
        'bounty'         => 1000,
        'anon'           => $torrentRequest->anon,
    ]);
});

test('update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->moderator()->create(['seedbonus' => 100000]);
    $category = Category::factory()->create(['tv_meta' => true]);
    $torrentRequest = TorrentRequest::factory()
        ->for($user)
        ->for($category)
        ->create(['bounty' => 1000]);

    $newTorrentRequest = TorrentRequest::factory()
        ->for($user)
        ->for($category)
        ->make();

    Http::fake();

    $this->actingAs($user)
        ->patch(route('requests.update', [$torrentRequest]), [
            'name'                 => $newTorrentRequest->name,
            'title_exists_on_imdb' => true,
            'imdb'                 => $newTorrentRequest->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $newTorrentRequest->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $newTorrentRequest->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $newTorrentRequest->mal,
            'season_number'        => $newTorrentRequest->season_number,
            'episode_number'       => $newTorrentRequest->episode_number,
            'category_id'          => $newTorrentRequest->category_id,
            'type_id'              => $newTorrentRequest->type_id,
            'resolution_id'        => $newTorrentRequest->resolution_id,
            'description'          => $newTorrentRequest->description,
            'anon'                 => $newTorrentRequest->anon,
        ])
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.edited-request'));

    $this->assertDatabaseHas('requests', [
        'id'             => $torrentRequest->id,
        'name'           => $newTorrentRequest->name,
        'imdb'           => $newTorrentRequest->imdb,
        'tvdb'           => $newTorrentRequest->tvdb,
        'tmdb_tv_id'     => $newTorrentRequest->tmdb_tv_id,
        'mal'            => $newTorrentRequest->mal,
        'season_number'  => $newTorrentRequest->season_number,
        'episode_number' => $newTorrentRequest->episode_number,
        'category_id'    => $newTorrentRequest->category_id,
        'type_id'        => $newTorrentRequest->type_id,
        'resolution_id'  => $newTorrentRequest->resolution_id,
        'description'    => $newTorrentRequest->description,
        'anon'           => $newTorrentRequest->anon,
    ]);
});
