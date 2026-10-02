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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Enums\ModerationStatus;
use App\Http\Livewire\TorrentSearch;
use App\Models\Category;
use App\Models\Group;
use App\Models\MediaWork;
use App\Models\Torrent;
use App\Models\User;
use Livewire\Livewire;

/**
 * Behavioural regressions for the results panel being a Livewire 4 island
 * (see resources/views/livewire/torrent-search.blade.php): name search must
 * stay scoped to the results island (never re-sending the filter form), a
 * category change must still fall through to a full, unscoped re-render that
 * refreshes the island too, and the periodic live-stats refresh
 * (TorrentSearch::refreshDisplayedStats()) must surface real count changes
 * to the client while never disclosing a torrent whose moderation/visibility
 * changed after the original search.
 */
function islandSearchModerator(): User
{
    return User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);
}

function islandSearchWorkWithTorrent(Category $category, User $moderator, string $title, array $overrides = []): Torrent
{
    $work = MediaWork::query()->create([
        'identity_key' => 'island-test:'.Illuminate\Support\Str::uuid(),
        'kind'         => 'other',
        'title'        => $title,
    ]);

    return Torrent::factory()->create(array_merge([
        'category_id'    => $category->id,
        'status'         => ModerationStatus::APPROVED,
        'moderated_by'   => $moderator->id,
        'media_work_id'  => $work->id,
        'name'           => $title.' Release',
        'tmdb_movie_id'  => null,
        'tmdb_tv_id'     => null,
        'igdb'           => null,
        'season_number'  => null,
        'episode_number' => null,
    ], $overrides));
}

test('a name search scoped to the results island never includes the filter form', function (): void {
    $category = Category::factory()->create([
        'name'      => 'IslandCategory1', 'no_meta' => false, 'music_meta' => false,
        'book_meta' => false, 'game_meta' => false, 'tv_meta' => false, 'movie_meta' => false,
    ]);
    $moderator = islandSearchModerator();
    islandSearchWorkWithTorrent($category, $moderator, 'Island Scoped Title');
    islandSearchWorkWithTorrent($category, $moderator, 'Unrelated Album');

    $this->actingAs($moderator);

    $component = Livewire::test(TorrentSearch::class)
        ->update(updates: ['driver' => 'sql']);

    $state = $component->update(
        calls: [[
            'method'   => '$commit',
            'params'   => [],
            'path'     => '',
            'metadata' => ['island' => ['name' => 'results', 'mode' => 'morph']],
        ]],
        updates: ['name' => 'Island Scoped Title'],
    );

    expect($state->effects)->toHaveKey('islandFragments');
    expect($state->effects)->not->toHaveKey('html');

    $fragment = implode('', $state->effects['islandFragments']);
    expect($fragment)->toContain('Island Scoped Title');
    expect($fragment)->not->toContain('Unrelated Album');
    expect($fragment)->not->toContain('torrent-search__filters');
});

test('an unscoped categoryIds change still fully re-renders the filter form and refreshes results', function (): void {
    $category = Category::factory()->create([
        'name'      => 'IslandCategory2', 'no_meta' => false, 'music_meta' => false,
        'book_meta' => false, 'game_meta' => false, 'tv_meta' => false, 'movie_meta' => false,
    ]);
    $moderator = islandSearchModerator();
    islandSearchWorkWithTorrent($category, $moderator, 'Island Unscoped Title');

    $this->actingAs($moderator);

    $component = Livewire::test(TorrentSearch::class)
        ->update(updates: ['driver' => 'sql']);

    $state = $component->update(updates: ['categoryIds' => [$category->id]]);

    expect($state->effects)->toHaveKey('html');
    expect($state->effects['html'])->toContain('torrent-search__filters');
    expect($state->effects['html'])->toContain('Island Unscoped Title');
});

test('refreshDisplayedStats dispatches the torrent\'s current live seeder/leecher/completed counts without rendering any HTML', function (): void {
    $category = Category::factory()->create([
        'name'      => 'IslandCategory3', 'no_meta' => false, 'music_meta' => false,
        'book_meta' => false, 'game_meta' => false, 'tv_meta' => false, 'movie_meta' => false,
    ]);
    $moderator = islandSearchModerator();
    $torrent = islandSearchWorkWithTorrent($category, $moderator, 'Island Stats Title', [
        'seeders' => 1, 'leechers' => 2, 'times_completed' => 3,
    ]);

    $this->actingAs($moderator);

    $component = Livewire::test(TorrentSearch::class)
        ->update(updates: ['driver' => 'sql', 'categoryIds' => [$category->id]]);

    // The count changes between the original search and the poll (e.g. a
    // peer connects/disconnects) must be visible to the client.
    $torrent->update(['seeders' => 5, 'leechers' => 7, 'times_completed' => 9]);

    $state = $component->update(calls: [[
        'method'   => 'refreshDisplayedStats',
        'params'   => [],
        'path'     => '',
        'metadata' => ['island' => ['name' => 'results', 'mode' => 'morph']],
    ]]);

    expect($state->effects)->not->toHaveKey('html');
    expect($state->effects)->not->toHaveKey('islandFragments');

    $component->assertDispatched('torrent-stats-refreshed', function ($name, $params) use ($torrent) {
        $updated = collect($params['torrents'])->firstWhere('id', $torrent->id);

        return $updated !== null
            && $updated['seeders'] === 5
            && $updated['leechers'] === 7
            && $updated['timesCompleted'] === 9;
    });
});

test('refreshDisplayedStats never discloses a torrent unapproved or soft-deleted after the original search', function (): void {
    $category = Category::factory()->create([
        'name'      => 'IslandCategory4', 'no_meta' => false, 'music_meta' => false,
        'book_meta' => false, 'game_meta' => false, 'tv_meta' => false, 'movie_meta' => false,
    ]);
    $moderator = islandSearchModerator();
    $rejected = islandSearchWorkWithTorrent($category, $moderator, 'Island Rejected Title');
    $deleted = islandSearchWorkWithTorrent($category, $moderator, 'Island Deleted Title');
    $stillVisible = islandSearchWorkWithTorrent($category, $moderator, 'Island Visible Title');

    $this->actingAs($moderator);

    $component = Livewire::test(TorrentSearch::class)
        ->update(updates: ['driver' => 'sql', 'categoryIds' => [$category->id]]);

    // Moderation/soft-delete state changes after the original search ran.
    $rejected->update(['status' => ModerationStatus::REJECTED]);
    $deleted->delete();

    $state = $component->update(calls: [[
        'method'   => 'refreshDisplayedStats',
        'params'   => [],
        'path'     => '',
        'metadata' => ['island' => ['name' => 'results', 'mode' => 'morph']],
    ]]);

    $component->assertDispatched('torrent-stats-refreshed', function ($name, $params) use ($rejected, $deleted, $stillVisible) {
        $ids = collect($params['torrents'])->pluck('id');

        return $ids->contains($stillVisible->id)
            && ! $ids->contains($rejected->id)
            && ! $ids->contains($deleted->id);
    });

    expect($state->effects)->not->toHaveKey('html');
});
