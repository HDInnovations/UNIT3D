<?php

declare(strict_types=1);

use App\Models\MediaLanguage;
use App\Models\Subtitle;
use App\Models\Torrent;

test('torrent searchable array includes subtitle language ids', function (): void {
    $language = MediaLanguage::factory()->create(['code' => 'en']);
    $torrent = Torrent::factory()->create();

    Subtitle::factory()->create([
        'language_id' => $language->id,
        'torrent_id'  => $torrent->id,
    ]);

    expect($torrent->toSearchableArray()['subtitle_language_ids'])->toContain($language->id);
});
