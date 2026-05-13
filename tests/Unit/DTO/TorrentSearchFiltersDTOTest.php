<?php

declare(strict_types=1);

use App\DTO\TorrentSearchFiltersDTO;
use App\Enums\UserGroup;
use App\Models\Torrent;
use App\Models\User;

test('it filters torrents by bdinfo text', function (): void {
    $this->actingAs(User::factory()->create([
        'group_id' => UserGroup::USER->value,
    ]));

    $matching = Torrent::factory()->create([
        'bdinfo' => 'Audio: French DTS-HD Master Audio',
    ]);

    Torrent::factory()->create([
        'bdinfo' => 'Audio: English Dolby TrueHD',
    ]);

    $torrentIds = Torrent::query()
        ->where((new TorrentSearchFiltersDTO(bdinfo: 'French DTS-HD'))->toSqlQueryBuilder())
        ->pluck('id')
        ->all();

    expect($torrentIds)->toBe([$matching->id]);
});
