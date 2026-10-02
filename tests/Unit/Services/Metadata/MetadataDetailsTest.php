<?php

declare(strict_types=1);

use App\Services\Metadata\MetadataDetails;

it('distinguishes IGDB developers from publishers while allowing a company to be both', function (): void {
    $rows = MetadataDetails::forSource('igdb', [
        'name' => 'Example Game',
        'involved_companies' => [
            ['company' => ['name' => 'Dev Only'], 'developer' => true, 'publisher' => false],
            ['company' => ['name' => 'Publisher Only'], 'developer' => false, 'publisher' => true],
            ['company' => ['name' => 'Both Studio'], 'developer' => true, 'publisher' => true],
        ],
    ]);

    $values = collect($rows)->pluck('value', 'label');

    expect($values->get(__('metadata.fields.developers')))->toBe('Dev Only, Both Studio');
    expect($values->get(__('metadata.fields.publishers')))->toBe('Publisher Only, Both Studio');
});

it('handles a sparse IGDB payload without a first_release_date', function (): void {
    $rows = MetadataDetails::forSource('IGDB', [
        'name' => 'No Date Game',
    ]);

    expect($rows)->toBe([]);
});

it('retains a meaningful zero rating/vote count instead of omitting it', function (): void {
    $rows = MetadataDetails::forSource('igdb', [
        'name'         => 'Zero Rated Game',
        'rating'       => 0,
        'rating_count' => 0,
    ]);

    $values = collect($rows)->pluck('value', 'label');

    expect($values->get(__('metadata.fields.rating')))->toBe('0');
    expect($values->get(__('metadata.fields.rating_count')))->toBe('0');
});

it('retains a zero TMDB vote_count and false adult flag', function (): void {
    $rows = MetadataDetails::forSource('tmdb', [
        'title'        => 'New Release',
        'vote_average' => 0,
        'vote_count'   => 0,
        'adult'        => false,
    ]);

    $values = collect($rows)->pluck('value', 'label');

    expect($values->get(__('metadata.fields.rating')))->toBe('0');
    expect($values->get(__('metadata.fields.rating_count')))->toBe('0');
    expect($values->get(__('metadata.fields.adult')))->toBe(__('metadata.fields.no'));
});

it('builds a multi-disc MusicBrainz tracklist with formatted durations', function (): void {
    $rows = MetadataDetails::forSource('musicbrainz', [
        'id'    => 'mbid',
        'title' => 'Example Release',
        'media' => [
            [
                'position' => 1,
                'format'   => 'CD',
                'tracks'   => [
                    ['position' => 1, 'title' => 'Intro', 'length' => 65_000],
                    ['position' => 2, 'title' => 'Second Song', 'length' => 200_000],
                ],
            ],
            [
                'position' => 2,
                'format'   => 'CD',
                'tracks'   => [
                    ['position' => 1, 'title' => 'Bonus Track', 'length' => 90_000],
                ],
            ],
        ],
    ]);

    $tracklistLabel = __('metadata.fields.tracklist');
    $tracklists = collect($rows)->where('label', $tracklistLabel)->pluck('value')->values();

    expect($tracklists)->toHaveCount(2);
    expect($tracklists[0])->toBe(__('metadata.fields.disc', ['number' => 1]).": 1. Intro (1:05)\n2. Second Song (3:20)");
    expect($tracklists[1])->toBe(__('metadata.fields.disc', ['number' => 2]).': 1. Bonus Track (1:30)');
});

it('omits unavailable fields instead of fabricating them', function (): void {
    $rows = MetadataDetails::forSource('open-library', [
        'title' => 'Untitled Book',
        'key'   => '/books/OL1M',
    ]);

    expect($rows)->toBe([]);
});

it('prefers the age_rating_category.rating relation over a bare enum id', function (): void {
    $rows = MetadataDetails::forSource('igdb', [
        'name' => 'Rated Game',
        'age_ratings' => [
            ['organization' => ['name' => 'ESRB'], 'rating_category' => ['id' => 5, 'rating' => 'Mature']],
            ['organization' => ['name' => 'PEGI'], 'rating_category' => 12],
        ],
    ]);

    $value = collect($rows)->firstWhere('label', __('metadata.fields.age_ratings'))['value'] ?? null;

    expect($value)->toBe('ESRB Mature');
});

it('summarizes multiplayer flags and counts instead of a fabricated yes', function (): void {
    $rowsWithFalseFlags = MetadataDetails::forSource('igdb', [
        'name'              => 'No Real Multiplayer',
        'multiplayer_modes' => [
            ['campaigncoop' => false, 'onlinecoop' => false],
        ],
    ]);

    expect(collect($rowsWithFalseFlags)->pluck('label'))->not->toContain(__('metadata.fields.multiplayer'));

    $rowsWithFlags = MetadataDetails::forSource('igdb', [
        'name'              => 'Co-op Game',
        'multiplayer_modes' => [
            ['onlinecoop' => true, 'onlinemax' => 4],
        ],
    ]);

    $value = collect($rowsWithFlags)->firstWhere('label', __('metadata.fields.multiplayer'))['value'] ?? null;

    expect($value)->toBe(
        __('metadata.fields.multiplayer_online_coop').', '
        .__('metadata.fields.multiplayer_online_max').' 4'
    );
});

it('treats stored source aliases as equivalent to their display labels', function (): void {
    $viaAlias = MetadataDetails::forSource('igdb', ['name' => 'X', 'rating' => 1]);
    $viaLabel = MetadataDetails::forSource('IGDB', ['name' => 'X', 'rating' => 1]);

    expect($viaAlias)->toBe($viaLabel);
});
