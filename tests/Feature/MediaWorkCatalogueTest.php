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
use App\Models\Category;
use App\Models\MediaWork;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use App\Services\Media\MediaWorkCatalog;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Deterministic, isolated behavioural regressions for the grouped catalogue
 * feature (see CONTEXT.md: Work -> Edition -> Variant -> Torrent). Exercises
 * App\Services\Media\MediaWorkCatalog directly against real factories/stored
 * metadata (no external HTTP), plus the HTTP-facing search/detail privacy
 * boundary and the category edition filter SQL, matching the shared
 * catalogue-enhancements contract.
 */
function catalogueCategory(array $overrides = []): Category
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

function catalogueTorrent(Category $category, array $overrides = []): Torrent
{
    return Torrent::factory()->create(array_merge([
        'category_id'   => $category->id,
        'status'        => ModerationStatus::APPROVED,
        'moderated_by'  => \App\Models\User::factory()->create()->id,
        'tmdb_movie_id' => null,
        'tmdb_tv_id'    => null,
        'igdb'          => null,
        'season_number' => null,
        'episode_number' => null,
    ], $overrides));
}

// --- TV: same Work across seasons/quality, independent stats ---------------

test('same tmdb tv work with differing season and quality shares one Work while each torrent keeps its own stats', function (): void {
    $category = catalogueCategory(['tv_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $s01 = catalogueTorrent($category, [
        'tmdb_tv_id'   => 4242,
        'season_number' => 1,
        'episode_number' => 0,
        'seeders'      => 10,
        'leechers'     => 2,
        'info_hash'    => str_repeat('a', 20),
    ]);
    $s02 = catalogueTorrent($category, [
        'tmdb_tv_id'   => 4242,
        'season_number' => 2,
        'episode_number' => 0,
        'seeders'      => 99,
        'leechers'     => 7,
        'info_hash'    => str_repeat('b', 20),
    ]);

    $workA = $catalog->attach($s01);
    $workB = $catalog->attach($s02);

    expect($workA->id)->toBe($workB->id);
    expect($workA->identity_key)->toBe('tv:tmdb:4242');

    $s01->refresh();
    $s02->refresh();

    // Each variant keeps its own independent swarm stats/infohash under the
    // shared Work; attaching never mutates sibling torrent rows.
    expect($s01->seeders)->toBe(10)
        ->and($s01->leechers)->toBe(2)
        ->and($s02->seeders)->toBe(99)
        ->and($s02->leechers)->toBe(7)
        ->and(bin2hex($s01->info_hash))->not->toBe(bin2hex($s02->info_hash));

    expect($workA->torrents()->pluck('id')->sort()->values()->all())
        ->toBe(collect([$s01->id, $s02->id])->sort()->values()->all());
});

test('identical numeric ids never collide across movie and tv kinds', function (): void {
    $movieCategory = catalogueCategory(['movie_meta' => true]);
    $tvCategory = catalogueCategory(['tv_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $movie = catalogueTorrent($movieCategory, ['tmdb_movie_id' => 777]);
    $tv = catalogueTorrent($tvCategory, ['tmdb_tv_id' => 777]);

    $movieWork = $catalog->attach($movie);
    $tvWork = $catalog->attach($tv);

    expect($movieWork->id)->not->toBe($tvWork->id);
    expect($movieWork->identity_key)->toBe('movie:tmdb:777');
    expect($tvWork->identity_key)->toBe('tv:tmdb:777');
    expect($movieWork->kind)->toBe('movie');
    expect($tvWork->kind)->toBe('tv');
});

// --- Provider-less content: no implicit filename merge, explicit merge survives sync ---

test('providerless torrents sharing a filename are never implicitly merged, but explicit existing-work selection persists across sync', function (): void {
    $category = catalogueCategory(); // all *_meta false => kind "no"
    $catalog = app(MediaWorkCatalog::class);

    $first = catalogueTorrent($category, ['name' => 'Some.Release.Name-GRP']);
    $second = catalogueTorrent($category, ['name' => 'Some.Release.Name-GRP']);

    $firstWork = $catalog->attach($first);
    $secondWork = $catalog->attach($second);

    // Same filename, no provider id: never silently merged.
    expect($firstWork->id)->not->toBe($secondWork->id);

    // Explicit user choice ("add quality variant to this title") merges it.
    $merged = $catalog->attach($second->refresh(), null, $firstWork->id);
    expect($merged->id)->toBe($firstWork->id);

    $second->refresh();
    expect($second->media_work_id)->toBe($firstWork->id);

    // A later sync() (e.g. after editing the torrent) must not silently
    // undo the explicit merge back onto a fresh provisional identity.
    $resynced = $catalog->sync($second);
    expect($resynced->id)->toBe($firstWork->id);

    $second->refresh();
    expect($second->media_work_id)->toBe($firstWork->id);
});

// --- Privacy: pending-only Work is invisible to search/detail/explicit selection ---

test('a Work whose only torrent is pending is invisible to search and detail, and explicit selection of it is rejected even on a canonical identity match', function (): void {
    $category = catalogueCategory(['tv_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $pending = catalogueTorrent($category, [
        'tmdb_tv_id' => 5150,
        'status'     => ModerationStatus::PENDING,
        'name'       => 'Hidden Pending Title',
    ]);

    // attach() itself doesn't gate on visibility (it's the uploader's own
    // torrent being newly linked); the guard is on read/selection paths.
    $work = $catalog->attach($pending);

    expect($work->torrents()->exists())->toBeFalse();

    // Search must not surface it.
    $this->asAuthenticatedUser();
    $response = $this->getJson('/works/search?'.http_build_query([
        'category_id' => $category->id,
        'query'       => 'Hidden',
    ]));
    $response->assertOk();
    expect($response->json('results'))->toBe([]);

    // Detail must 404 rather than leak shared metadata.
    $this->get("/works/{$work->id}")->assertNotFound();

    // A second, canonically-identical torrent explicitly selecting this
    // (still fully hidden) Work must be rejected, even though its own
    // provider identity matches exactly.
    $prospective = new Torrent();
    $prospective->setAttribute('category_id', $category->id);
    $prospective->setAttribute('tmdb_tv_id', 5150);

    expect(fn () => $catalog->validateSelection($prospective, null, $work->id))
        ->toThrow(ValidationException::class);
});

// --- Music: release + release-group share the same Work -------------------

test('a specific musicbrainz release and its own release-group share the same Work', function (): void {
    $category = catalogueCategory(['music_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);
    $groupId = 'group-uuid-1234';

    $releaseTorrent = catalogueTorrent($category, ['name' => 'Album (2020) [FLAC]']);
    TorrentMetadata::query()->create([
        'torrent_id'  => $releaseTorrent->id,
        'source'      => 'musicbrainz',
        'source_id'   => 'release-uuid-aaaa',
        'title'       => 'Album',
        'source_url'  => 'https://musicbrainz.org/release/release-uuid-aaaa',
        'raw'         => ['id' => 'release-uuid-aaaa', 'release-group' => ['id' => $groupId]],
    ]);

    $groupTorrent = catalogueTorrent($category, ['name' => 'Album (2020) [MP3]']);
    TorrentMetadata::query()->create([
        'torrent_id'  => $groupTorrent->id,
        'source'      => 'musicbrainz',
        'source_id'   => $groupId,
        'title'       => 'Album',
        'source_url'  => "https://musicbrainz.org/release-group/{$groupId}",
        'raw'         => ['id' => $groupId],
    ]);

    $releaseWork = $catalog->sync($releaseTorrent);
    $groupWork = $catalog->sync($groupTorrent);

    expect($releaseWork->id)->toBe($groupWork->id);
    expect($releaseWork->identity_key)->toBe("music:musicbrainz:{$groupId}");
});

test('music label and year match the accessible edition rather than the last shared snapshot', function (): void {
    $category = catalogueCategory(['music_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);
    $groupId = (string) Str::uuid();
    $editions = [];

    foreach (['1998' => 'First Label', '2008' => 'Second Label'] as $year => $label) {
        $torrent = catalogueTorrent($category);
        $releaseId = (string) Str::uuid();
        TorrentMetadata::query()->create([
            'torrent_id' => $torrent->id, 'source' => 'musicbrainz', 'source_id' => $releaseId,
            'title' => 'Album', 'source_url' => "https://musicbrainz.org/release/{$releaseId}",
            'raw' => ['id' => $releaseId, 'date' => "{$year}-01-01", 'release-group' => ['id' => $groupId],
                'label-info' => [['label' => ['name' => $label]]]],
            'facets' => $catalog->computeFacets('music', ['date' => "{$year}-01-01", 'label-info' => [['label' => ['name' => $label]]]]),
        ]);
        $catalog->sync($torrent);
        $editions[] = $torrent;
    }

    $this->asAuthenticatedUser();
    expect($editions[0]->fresh()->media_work_id)->toBe($editions[1]->fresh()->media_work_id);
    expect(Torrent::query()->where((new TorrentSearchFiltersDTO(musicLabel: 'First Label', musicYear: 1998))->toSqlQueryBuilder())->pluck('id')->all())
        ->toBe([$editions[0]->id]);
});

test('legacy source caches cannot erase a complete shared source snapshot', function (): void {
    $category = catalogueCategory(['movie_meta' => true]);
    $movie = App\Models\TmdbMovie::factory()->create(['raw' => null]);
    $torrent = catalogueTorrent($category, ['tmdb_movie_id' => $movie->id]);
    $raw = ['id' => $movie->id, 'genres' => [['id' => 35, 'name' => 'Comedy']]];
    $catalog = app(MediaWorkCatalog::class);
    $work = $catalog->attach($torrent, ['title' => $movie->title, 'source' => 'TMDB', 'identifiers' => ['tmdb_movie_id' => $movie->id], 'raw' => $raw]);

    expect($catalog->sync($torrent)->raw)->toBe($raw);
    expect($work->fresh()->raw)->toBe($raw);
});

// --- Book: Open Library editions sharing works[0].key merge ---------------

test('two Open Library editions of the same work merge into one Work, retaining a usable edition source id', function (): void {
    $category = catalogueCategory(['book_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $editionA = catalogueTorrent($category, ['name' => 'Book Title (First Printing)']);
    TorrentMetadata::query()->create([
        'torrent_id' => $editionA->id,
        'source'     => 'open-library',
        'source_id'  => 'OL1A',
        'title'      => 'Book Title',
        'source_url' => 'https://openlibrary.org/books/OL1A',
        'raw'        => ['key' => '/books/OL1A', 'works' => [['key' => '/works/OLW9']]],
    ]);

    $editionB = catalogueTorrent($category, ['name' => 'Book Title (Reprint)']);
    TorrentMetadata::query()->create([
        'torrent_id' => $editionB->id,
        'source'     => 'open-library',
        'source_id'  => 'OL2B',
        'title'      => 'Book Title',
        'source_url' => 'https://openlibrary.org/books/OL2B',
        'raw'        => ['key' => '/books/OL2B', 'works' => [['key' => '/works/OLW9']]],
    ]);

    $workA = $catalog->sync($editionA);
    $workB = $catalog->sync($editionB);

    expect($workA->id)->toBe($workB->id);
    expect($workA->identity_key)->toBe('book:open-library-work:OLW9');

    // The Work's usable source id stays a real, re-lookup-able edition id
    // (the just-synced edition's own OLID), never the internal work key.
    expect($workB->fresh()->source_id)->toBe('OL2B');
});

// --- Book: Google Books raw item id stays canonical identity across attach + persisted metadata + sync, despite differing ISBN ---

test('a Google Books canonical identity stays the same from trusted attach through persisted metadata and sync, even though the usable source id (ISBN) differs from the stored volume id', function (): void {
    $category = catalogueCategory(['book_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $raw = [
        'title'               => 'Some Novel',
        'google_books_item'   => ['id' => 'VOLUME_XYZ'],
        'industryIdentifiers' => [
            ['type' => 'ISBN_13', 'identifier' => '9781234567897'],
        ],
    ];

    $torrent = catalogueTorrent($category, ['name' => 'Some Novel']);

    // Trusted attach-time lookup (e.g. from MetadataSelectionStore), as
    // TorrentController::store would pass it.
    $lookup = [
        'title'       => 'Some Novel',
        'source'      => 'Google Books',
        'source_url'  => 'https://books.google.com/books?id=VOLUME_XYZ',
        'identifiers' => [],
        'raw'         => $raw,
    ];

    $attachedWork = $catalog->attach($torrent, $lookup);

    expect($attachedWork->identity_key)->toBe('book:google-books:VOLUME_XYZ');
    // The Work's usable lookup id is the ISBN, never the internal volume id.
    expect($attachedWork->source_id)->toBe('9781234567897');

    // A metadata job later persists this exact payload (its own source_id
    // column always stores the raw volume id, per ProcessOpenLibraryEditionJob).
    TorrentMetadata::query()->create([
        'torrent_id' => $torrent->id,
        'source'     => 'google-books',
        'source_id'  => 'VOLUME_XYZ',
        'title'      => 'Some Novel',
        'source_url' => 'https://books.google.com/books?id=VOLUME_XYZ',
        'raw'        => $raw,
    ]);

    $syncedWork = $catalog->sync($torrent->refresh());

    // Same canonical Work, identity key unaffected by the ISBN vs volume id
    // distinction; re-sync never relocates the Work.
    expect($syncedWork->id)->toBe($attachedWork->id);
    expect($syncedWork->identity_key)->toBe('book:google-books:VOLUME_XYZ');
    expect($syncedWork->source_id)->toBe('9781234567897');
});

// --- apply() refuses to relocate a Work onto a mismatched refresh ----------

test('apply() rejects a refresh lookup whose own canonical identity contradicts the target Work', function (): void {
    $catalog = app(MediaWorkCatalog::class);

    $work = MediaWork::query()->create([
        'identity_key' => 'movie:tmdb:100',
        'kind'         => 'movie',
        'source'       => 'tmdb',
        'source_id'    => '100',
        'title'        => 'Original Movie',
    ]);

    $mismatchedLookup = [
        'title'       => 'A Completely Different Movie',
        'source'      => 'TMDB',
        'identifiers' => ['tmdb_movie_id' => 999],
        'raw'         => [],
    ];

    expect(fn () => $catalog->apply($work, $mismatchedLookup))
        ->toThrow(InvalidArgumentException::class);

    // Rejected refresh must not have mutated the Work.
    expect($work->fresh()->title)->toBe('Original Movie');
    expect($work->fresh()->identity_key)->toBe('movie:tmdb:100');
});

// --- Category edition filters: match any accessible edition, exclude hidden ---

test('book language filter matches an earlier accessible edition after a later edition refreshes to a different language, and excludes a hidden matching edition', function (): void {
    $category = catalogueCategory(['book_meta' => true]);
    $catalog = app(MediaWorkCatalog::class);

    $work = MediaWork::query()->create([
        'identity_key' => 'book:open-library-work:OLW-FILTER',
        'kind'         => 'book',
        'title'        => 'Filtered Book',
    ]);

    $englishEdition = catalogueTorrent($category, ['media_work_id' => $work->id]);
    TorrentMetadata::query()->create([
        'torrent_id' => $englishEdition->id,
        'source'     => 'open-library',
        'source_id'  => 'OL-EN',
        'title'      => 'Filtered Book',
        'source_url' => 'https://openlibrary.org/books/OL-EN',
        'raw'        => [],
        'facets'     => ['authors' => [], 'languages' => ['English'], 'publishers' => []],
    ]);

    $frenchEdition = catalogueTorrent($category, ['media_work_id' => $work->id]);
    TorrentMetadata::query()->create([
        'torrent_id' => $frenchEdition->id,
        'source'     => 'open-library',
        'source_id'  => 'OL-FR',
        'title'      => 'Filtered Book',
        'source_url' => 'https://openlibrary.org/books/OL-FR',
        'raw'        => [],
        'facets'     => ['authors' => [], 'languages' => ['French'], 'publishers' => []],
    ]);

    $hiddenGermanEdition = catalogueTorrent($category, [
        'media_work_id' => $work->id,
        'status'        => ModerationStatus::PENDING,
    ]);
    TorrentMetadata::query()->create([
        'torrent_id' => $hiddenGermanEdition->id,
        'source'     => 'open-library',
        'source_id'  => 'OL-DE',
        'title'      => 'Filtered Book',
        'source_url' => 'https://openlibrary.org/books/OL-DE',
        'raw'        => [],
        'facets'     => ['authors' => [], 'languages' => ['German'], 'publishers' => []],
    ]);

    $this->asAuthenticatedUser();

    $matchingEnglish = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(bookLanguage: 'English'))->toSqlQueryBuilder())
        ->pluck('id')
        ->all();

    expect($matchingEnglish)->toBe([$englishEdition->id]);

    // A hidden (pending) edition matching the filter must never surface.
    $matchingGerman = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(bookLanguage: 'German'))->toSqlQueryBuilder())
        ->pluck('id')
        ->all();
    expect($matchingGerman)->toBe([]);
});
