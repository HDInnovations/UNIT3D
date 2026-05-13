<?php

declare(strict_types=1);

use App\Http\Middleware\UpdateLastAction;
use App\Models\Category;
use App\Models\Group;
use App\Models\TmdbMovie;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

beforeEach(function (): void {
    $this->withoutMiddleware([
        ThrottleRequestsWithRedis::class,
        UpdateLastAction::class,
    ]);
});

test('similar page shows metadata note', function (): void {
    $category = Category::factory()->create([
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $movie = TmdbMovie::factory()->create([
        'note'         => 'Watch this in production order.',
        'runtime'      => '120',
        'vote_average' => '8.2',
    ]);
    $torrent = Torrent::factory()->create([
        'category_id'   => $category->id,
        'tmdb_movie_id' => $movie->id,
        'tmdb_tv_id'    => null,
        'igdb'          => null,
    ]);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('torrents.similar', [
        'category_id' => $category->id,
        'tmdb'        => $movie->id,
    ]));

    $response->assertOk();
    $response->assertSee('Watch this in production order.');
    $response->assertSee($torrent->name);
});

test('torrent staff can update similar page note', function (): void {
    $category = Category::factory()->create([
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $movie = TmdbMovie::factory()->create();
    Torrent::factory()->create([
        'category_id'   => $category->id,
        'tmdb_movie_id' => $movie->id,
        'tmdb_tv_id'    => null,
        'igdb'          => null,
    ]);
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create([
            'is_torrent_modo' => true,
        ])->id,
    ]);

    $response = $this->actingAs($staff)->patch(route('torrents.similar.update', [
        'category' => $category,
        'metaId'   => $movie->id,
    ]), [
        'note' => 'Use the DVD order for season one.',
    ]);

    $response->assertRedirect();
    expect($movie->fresh()->note)->toBe('Use the DVD order for season one.');
});

test('regular users cannot update similar page note', function (): void {
    $category = Category::factory()->create([
        'movie_meta' => true,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $movie = TmdbMovie::factory()->create([
        'note' => 'Original note.',
    ]);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('torrents.similar.update', [
        'category' => $category,
        'metaId'   => $movie->id,
    ]), [
        'note' => 'Changed note.',
    ]);

    $response->assertForbidden();
    expect($movie->fresh()->note)->toBe('Original note.');
});
