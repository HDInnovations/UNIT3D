<?php

declare(strict_types=1);

use App\Services\Igdb\IgdbClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('fetches a game through Twitch-authenticated IGDB API requests', function (): void {
    config()->set('igdb.credentials.client_id', 'client-id');
    config()->set('igdb.credentials.client_secret', 'client-secret');
    cache()->forget('igdb:access-token');

    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token' => 'access-token',
            'expires_in'   => 3_600,
        ]),
        'https://api.igdb.com/v4/games' => Http::response([[
            'id'   => 7,
            'name' => 'Example Game',
        ]]),
    ]);

    expect(app(IgdbClient::class)->game(7))->toBe([
        'id'   => 7,
        'name' => 'Example Game',
    ]);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://id.twitch.tv/oauth2/token'
        && $request['client_id'] === 'client-id'
        && $request['client_secret'] === 'client-secret'
        && $request['grant_type'] === 'client_credentials');
    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.igdb.com/v4/games'
        && $request->hasHeader('Client-ID', 'client-id')
        && $request->hasHeader('Authorization', 'Bearer access-token')
        && str_contains($request->body(), 'where id = 7;'));
});
