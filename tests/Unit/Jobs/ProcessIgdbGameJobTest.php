<?php

declare(strict_types=1);

use App\Jobs\ProcessIgdbGameJob;
use App\Models\IgdbGame;
use App\Services\Igdb\IgdbClient;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set('igdb.credentials.client_id', 'client-id');
    config()->set('igdb.credentials.client_secret', 'client-secret');
    cache()->forget('igdb:access-token');
    cache()->forget('igdb-game-scraper:1942');
});

it('stores the entire raw IGDB payload alongside the mapped columns', function (): void {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token' => 'access-token',
            'expires_in'   => 3_600,
        ]),
        'https://api.igdb.com/v4/games' => Http::response([[
            'id'           => 1942,
            'name'         => 'Cyberpunk 2077',
            'summary'            => 'An open-world, action-adventure story.',
            'first_release_date' => 1_505_779_200,
            'game_engines'       => [['id' => 1, 'name' => 'REDengine 3']],
            'genres'       => [['id' => 5, 'name' => 'Role-playing (RPG)']],
            'involved_companies' => [[
                'developer' => true,
                'publisher' => true,
                'company'   => ['id' => 9, 'name' => 'CD Projekt RED'],
            ]],
            'platforms' => [['id' => 6, 'name' => 'PC (Microsoft Windows)']],
        ]]),
    ]);

    (new ProcessIgdbGameJob(1942))->handle(app(IgdbClient::class));

    $game = IgdbGame::query()->findOrFail(1942);

    expect($game->raw['game_engines'][0]['name'])->toBe('REDengine 3')
        ->and($game->raw['involved_companies'][0]['developer'])->toBeTrue()
        ->and($game->raw['involved_companies'][0]['publisher'])->toBeTrue()
        ->and($game->raw['genres'][0]['name'])->toBe('Role-playing (RPG)')
        ->and($game->first_release_date?->toDateTimeString())->toBe('2017-09-19 00:00:00')
        ->and($game->genres->pluck('name')->all())->toBe(['Role-playing (RPG)']);
});

it('replaces stale raw fields when the game is refetched', function (): void {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token' => 'access-token',
            'expires_in'   => 3_600,
        ]),
        'https://api.igdb.com/v4/games' => Http::sequence()
            ->push([[
                'id'      => 1942,
                'name'    => 'Cyberpunk 2077',
                'summary' => 'First fetch summary.',
                'themes'  => [['id' => 1, 'name' => 'Action']],
            ]])
            ->push([[
                'id'      => 1942,
                'name'    => 'Cyberpunk 2077',
                'summary' => 'Refreshed summary.',
            ]]),
    ]);

    (new ProcessIgdbGameJob(1942))->handle(app(IgdbClient::class));

    cache()->forget('igdb-game-scraper:1942');

    (new ProcessIgdbGameJob(1942))->handle(app(IgdbClient::class));

    $game = IgdbGame::query()->findOrFail(1942);

    expect($game->raw)->not->toHaveKey('themes')
        ->and($game->raw['summary'])->toBe('Refreshed summary.');
});
