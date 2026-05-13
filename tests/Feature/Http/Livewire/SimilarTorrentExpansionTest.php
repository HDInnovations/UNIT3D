<?php

declare(strict_types=1);

use App\Http\Livewire\SimilarTorrent;
use App\Models\Category;
use App\Models\Group;
use App\Models\TmdbTv;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('bulk delete keeps similar page groups expanded after clearing selection', function (): void {
    Notification::fake();

    $category = Category::factory()->create([
        'movie_meta' => false,
        'tv_meta'    => true,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $show = TmdbTv::factory()->create([
        'first_air_date' => '2024-01-01',
        'last_air_date'  => '2024-01-01',
    ]);
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create([
            'is_modo' => true,
        ])->id,
    ]);
    $deletedTorrent = Torrent::factory()->create([
        'category_id'    => $category->id,
        'tmdb_tv_id'     => $show->id,
        'tmdb_movie_id'  => null,
        'igdb'           => null,
        'season_number'  => 1,
        'episode_number' => 1,
    ]);
    $remainingTorrent = Torrent::factory()->create([
        'category_id'    => $category->id,
        'tmdb_tv_id'     => $show->id,
        'tmdb_movie_id'  => null,
        'igdb'           => null,
        'season_number'  => 1,
        'episode_number' => 2,
    ]);

    Livewire::actingAs($staff)
        ->test(SimilarTorrent::class, [
            'category' => $category,
            'work'     => $show,
            'tmdbId'   => $show->id,
            'igdbId'   => null,
        ])
        ->set('checked', [$deletedTorrent->id])
        ->assertSet('expandGroups', true)
        ->set('reason', 'Duplicate upload')
        ->call('deleteRecords')
        ->assertSet('checked', [])
        ->assertSet('selectPage', false)
        ->assertSet('expandGroups', true)
        ->assertSee($remainingTorrent->name);

    expect(Torrent::query()->whereKey($deletedTorrent->id)->exists())->toBeFalse();
});
