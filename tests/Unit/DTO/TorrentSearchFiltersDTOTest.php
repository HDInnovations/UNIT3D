<?php

declare(strict_types=1);

use App\DTO\TorrentSearchFiltersDTO;
use App\Models\MediaLanguage;
use App\Models\Subtitle;
use App\Models\Torrent;

test('sql torrent search filters by subtitle language', function (): void {
    $this->asAuthenticatedUser();

    $danish = MediaLanguage::factory()->create(['name' => 'Danish', 'code' => 'da']);
    $english = MediaLanguage::factory()->create(['name' => 'English', 'code' => 'en']);

    $danishTorrent = Torrent::factory()->create();
    $englishTorrent = Torrent::factory()->create();
    $torrentWithoutSubtitles = Torrent::factory()->create();

    Subtitle::factory()->create([
        'language_id' => $danish->id,
        'torrent_id'  => $danishTorrent->id,
    ]);

    Subtitle::factory()->create([
        'language_id' => $english->id,
        'torrent_id'  => $englishTorrent->id,
    ]);

    $torrentIds = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(subtitleLanguageIds: [$danish->id]))->toSqlQueryBuilder())
        ->pluck('id');

    expect($torrentIds)
        ->toContain($danishTorrent->id)
        ->not->toContain($englishTorrent->id)
        ->not->toContain($torrentWithoutSubtitles->id);
});

test('meilisearch torrent search filters by subtitle language ids', function (): void {
    $this->asAuthenticatedUser();

    $filters = (new TorrentSearchFiltersDTO(subtitleLanguageIds: ['2', '5']))->toMeilisearchFilter();

    expect($filters)->toContain('subtitle_language_ids IN [2,5]');
});
