<?php

declare(strict_types=1);

use App\Services\Tmdb\TMDB;

test('it selects the first supported trailer video', function (): void {
    $tmdb = new TMDB();

    $trailer = $tmdb->trailer([
        'videos' => [
            'results' => [
                [
                    'key'  => 'teaser-key',
                    'site' => 'YouTube',
                    'type' => 'Teaser',
                ],
                [
                    'key'  => 'vimeo-key',
                    'site' => 'Vimeo',
                    'type' => 'Trailer',
                ],
            ],
        ],
    ]);

    expect($trailer)->toBe('vimeo:vimeo-key');
});

test('it builds trailer embed urls for supported providers and legacy youtube keys', function (): void {
    $tmdb = new TMDB();

    expect($tmdb->trailerEmbedUrl('youtube:youtube-key'))->toBe('https://www.youtube-nocookie.com/embed/youtube-key')
        ->and($tmdb->trailerEmbedUrl('vimeo:vimeo-key'))->toBe('https://player.vimeo.com/video/vimeo-key')
        ->and($tmdb->trailerEmbedUrl('legacy-youtube-key'))->toBe('https://www.youtube-nocookie.com/embed/legacy-youtube-key')
        ->and($tmdb->trailerEmbedUrl('vimeo:'))->toBeNull()
        ->and($tmdb->trailerEmbedUrl('dailymotion:unsupported-key'))->toBeNull();
});
