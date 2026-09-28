<?php

declare(strict_types=1);

namespace App\Services\Igdb;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class IgdbClient
{
    /**
     * @return array<string, mixed>
     */
    public function game(int $id): array
    {
        $games = Http::acceptJson()
            ->withHeaders([
                'Client-ID' => $this->clientId(),
            ])
            ->withToken($this->accessToken())
            ->withBody(<<<'APICALYPSE'
fields id,name,summary,first_release_date,url,rating,rating_count,cover.image_id,artworks.image_id,genres.id,genres.name,videos.video_id,videos.name,involved_companies.company.id,involved_companies.company.name,involved_companies.company.url,involved_companies.company.logo.image_id,platforms.id,platforms.name,platforms.platform_logo.image_id;
APICALYPSE
                ."where id = {$id};\nlimit 1;", 'text/plain')
            ->post('https://api.igdb.com/v4/games')
            ->throw()
            ->json();

        if (!is_array($games) || !isset($games[0]) || !is_array($games[0])) {
            throw new RuntimeException("IGDB game {$id} was not found.");
        }

        return $games[0];
    }

    private function accessToken(): string
    {
        $cacheKey = 'igdb:access-token';
        $token = cache()->get($cacheKey);

        if (is_string($token) && $token !== '') {
            return $token;
        }

        $response = Http::asForm()
            ->post('https://id.twitch.tv/oauth2/token', [
                'client_id'     => $this->clientId(),
                'client_secret' => $this->clientSecret(),
                'grant_type'    => 'client_credentials',
            ])
            ->throw()
            ->json();
        $token = $response['access_token'] ?? null;
        $expiresIn = $response['expires_in'] ?? null;

        if (!is_string($token) || $token === '' || !is_int($expiresIn)) {
            throw new RuntimeException('Twitch did not return a usable IGDB access token.');
        }

        cache()->put($cacheKey, $token, now()->addSeconds(max(60, $expiresIn - 60)));

        return $token;
    }

    private function clientId(): string
    {
        $clientId = config('igdb.credentials.client_id');

        if (!is_string($clientId) || $clientId === '') {
            throw new RuntimeException('IGDB requires TWITCH_CLIENT_ID.');
        }

        return $clientId;
    }

    private function clientSecret(): string
    {
        $clientSecret = config('igdb.credentials.client_secret');

        if (!is_string($clientSecret) || $clientSecret === '') {
            throw new RuntimeException('IGDB requires TWITCH_CLIENT_SECRET.');
        }

        return $clientSecret;
    }
}
