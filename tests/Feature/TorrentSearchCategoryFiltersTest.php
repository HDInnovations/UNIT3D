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

use App\DTO\TorrentSearchFiltersDTO;
use App\Enums\ModerationStatus;
use App\Http\Livewire\TorrentSearch;
use App\Models\Category;
use App\Models\MediaWork;
use App\Models\Torrent;
use Illuminate\Support\Str;
use Livewire\Livewire;

/**
 * Behavioural regressions for:
 *  - the main torrent name search also matching the canonical Work title for
 *    kinds whose torrent filename doesn't carry the title (e.g. music);
 *  - stale movie/TV/technical filters no longer silently zeroing out results
 *    for every other kind after a category change or an explicit-category
 *    deep link.
 */
function categorySearchCategory(array $overrides = []): Category
{
    return Category::factory()->create(array_merge([
        'name'       => Str::random(12),
        'no_meta'    => false,
        'music_meta' => false,
        'book_meta'  => false,
        'game_meta'  => false,
        'tv_meta'    => false,
        'movie_meta' => false,
    ], $overrides));
}

function categorySearchTorrent(Category $category, array $overrides = []): Torrent
{
    return Torrent::factory()->create(array_merge([
        'category_id'    => $category->id,
        'status'         => ModerationStatus::APPROVED,
        'moderated_by'   => \App\Models\User::factory()->create()->id,
        'tmdb_movie_id'  => null,
        'tmdb_tv_id'     => null,
        'igdb'           => null,
        'season_number'  => null,
        'episode_number' => null,
    ], $overrides));
}

test('name search finds a music Work by its canonical title even though the torrent filename differs entirely', function (): void {
    $category = categorySearchCategory(['music_meta' => true]);

    $work = MediaWork::query()->create([
        'identity_key' => 'music:musicbrainz:'.Str::uuid(),
        'kind'         => 'music',
        'title'        => 'Wish You Were Here',
    ]);

    $matchingTorrent = categorySearchTorrent($category, [
        'name'          => 'VA-Compilation_Disc_04-WEB-2020-GROUP',
        'media_work_id' => $work->id,
    ]);

    $unrelatedTorrent = categorySearchTorrent($category, [
        'name' => 'Some Other Random Release',
    ]);

    $this->asAuthenticatedUser();

    $matching = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(name: 'Wish You Were Here'))->toSqlQueryBuilder())
        ->pluck('id')
        ->all();

    expect($matching)->toBe([$matchingTorrent->id])
        ->and($matching)->not->toContain($unrelatedTorrent->id);
});

test('regex name search also matches the canonical Work title, not only the torrent filename', function (): void {
    $category = categorySearchCategory(['book_meta' => true]);

    $work = MediaWork::query()->create([
        'identity_key' => 'book:open-library-work:OLW-REGEX',
        'kind'         => 'book',
        'title'        => 'The Great Gatsby',
    ]);

    $matchingTorrent = categorySearchTorrent($category, [
        'name'          => 'scan_0007_final.epub',
        'media_work_id' => $work->id,
    ]);

    $moderator = \App\Models\User::factory()->create([
        'group_id' => \App\Models\Group::factory()->create(['is_modo' => true])->id,
    ]);
    $this->actingAs($moderator);

    $matching = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(name: '/Great Gatsby/'))->toSqlQueryBuilder())
        ->pluck('id')
        ->all();

    expect($matching)->toBe([$matchingTorrent->id]);
});

test('changing categoryIds away from movie/tv clears hidden movie/TV/technical filters that would otherwise zero out music results', function (): void {
    $movieCategory = categorySearchCategory(['movie_meta' => true]);
    $musicCategory = categorySearchCategory(['music_meta' => true]);
    $videoType = App\Models\Type::factory()->create(['name' => 'HDTV']);

    $this->asAuthenticatedUser();

    Livewire::test(TorrentSearch::class)
        ->set('categoryIds', [$movieCategory->id])
        ->set('genreIds', [28])
        ->set('collectionId', 10)
        ->set('companyId', 20)
        ->set('tmdbId', 603)
        ->set('regionIds', [1])
        ->set('typeIds', [$videoType->id])
        ->set('resolutionIds', [3])
        ->set('startYear', 2000)
        ->set('endYear', 2010)
        ->set('categoryIds', [$musicCategory->id])
        ->assertSet('genreIds', [])
        ->assertSet('collectionId', null)
        ->assertSet('companyId', null)
        ->assertSet('tmdbId', null)
        ->assertSet('regionIds', [])
        ->assertSet('typeIds', [])
        ->assertSet('resolutionIds', [])
        ->assertSet('startYear', null)
        ->assertSet('endYear', null);
});

test('switching categoryIds to tv-only clears movie-only collectionId but keeps shared movie/tv genreIds', function (): void {
    $movieCategory = categorySearchCategory(['movie_meta' => true]);
    $tvCategory = categorySearchCategory(['tv_meta' => true]);

    $this->asAuthenticatedUser();

    Livewire::test(TorrentSearch::class)
        ->set('categoryIds', [$movieCategory->id])
        ->set('collectionId', 10)
        ->set('genreIds', [28])
        ->set('categoryIds', [$tvCategory->id])
        ->assertSet('collectionId', null)
        ->assertSet('genreIds', [28]);
});

test('an explicit-category deep link contradicted by a stale genreIds/resolutionIds query string is sanitized on initial load', function (): void {
    $musicCategory = categorySearchCategory(['music_meta' => true]);

    $this->asAuthenticatedUser();

    Livewire::withQueryParams([
        'categoryIds'   => [$musicCategory->id],
        'genreIds'      => [28],
        'resolutionIds' => [3],
    ])
        ->test(TorrentSearch::class)
        ->assertSet('genreIds', [])
        ->assertSet('resolutionIds', []);
});

test('an unscoped tmdbId deep link with no category remains untouched on initial load', function (): void {
    $this->asAuthenticatedUser();

    Livewire::withQueryParams([
        'tmdbId' => 603,
    ])
        ->test(TorrentSearch::class)
        ->assertSet('tmdbId', 603)
        ->assertSet('categoryIds', []);
});
