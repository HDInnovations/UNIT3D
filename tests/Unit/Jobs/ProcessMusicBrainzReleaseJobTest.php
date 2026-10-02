<?php

declare(strict_types=1);

use App\Jobs\ProcessMusicBrainzReleaseJob;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use Illuminate\Support\Facades\Http;

it('sums track counts across every disc and joins publishers across every label', function (): void {
    $torrent = Torrent::factory()->create();
    $mbid = '76df3287-6cda-33eb-8e9a-044b5e15ffdd';

    Http::fake([
        "https://musicbrainz.org/ws/2/release/{$mbid}*" => Http::response([
            'id'    => $mbid,
            'title' => 'Double Disc Release',
            'artist-credit' => [['name' => 'Sample Artist']],
            'label-info' => [
                ['label' => ['name' => 'First Label']],
                ['label' => ['name' => 'Second Label']],
            ],
            'media' => [
                ['tracks' => [['title' => 'A1'], ['title' => 'A2'], ['title' => 'A3']]],
                ['tracks' => [['title' => 'B1'], ['title' => 'B2']]],
            ],
        ], 200),
    ]);

    (new ProcessMusicBrainzReleaseJob($torrent->id, $mbid))->handle(app(App\Services\MusicBrainz\MusicBrainzClient::class));

    $metadata = TorrentMetadata::query()->where('torrent_id', $torrent->id)->firstOrFail();

    expect($metadata->item_count)->toBe(5)
        ->and($metadata->publisher)->toBe('First Label, Second Label')
        ->and($metadata->raw['media'])->toHaveCount(2);
});

it('persists album metadata without inventing edition publishers or track counts', function (): void {
    $torrent = Torrent::factory()->create();
    $mbid = 'df4b48f5-77fe-499d-a6a2-ca83d186beb8';
    $album = [
        'id' => $mbid, 'title' => 'Mockingbird', 'primary-type' => 'Single',
        'first-release-date' => '2005-04-25', 'artist-credit' => [['name' => 'Eminem']],
    ];
    Http::fake([
        "https://musicbrainz.org/ws/2/release/{$mbid}*" => Http::response(['error' => 'Not Found'], 404),
        "https://musicbrainz.org/ws/2/release-group/{$mbid}*" => Http::response($album, 200),
    ]);

    (new ProcessMusicBrainzReleaseJob($torrent->id, $mbid))->handle(app(App\Services\MusicBrainz\MusicBrainzClient::class));
    $metadata = TorrentMetadata::query()->where('torrent_id', $torrent->id)->firstOrFail();
    expect($metadata->title)->toBe('Mockingbird')
        ->and($metadata->subtitle)->toBe('Eminem')
        ->and($metadata->released_on)->toBe('2005-04-25')
        ->and($metadata->source_url)->toBe("https://musicbrainz.org/release-group/{$mbid}")
        ->and($metadata->publisher)->toBeNull()
        ->and($metadata->item_count)->toBeNull()
        ->and($metadata->raw)->toEqual($album);
});
