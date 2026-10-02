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
            ->connectTimeout(5)
            ->timeout(15)
            ->withHeaders([
                'Client-ID' => $this->clientId(),
            ])
            ->withToken($this->accessToken())
            ->withBody(<<<'APICALYPSE'
fields *,
    genres.*,
    game_engines.*,game_engines.logo.*,
    involved_companies.*,involved_companies.company.*,involved_companies.company.logo.*,
    platforms.*,platforms.platform_logo.*,
    themes.*,
    game_modes.*,
    player_perspectives.*,
    release_dates.*,release_dates.platform.id,release_dates.platform.name,release_dates.release_region.id,release_dates.release_region.region,release_dates.status.id,release_dates.status.name,
    age_ratings.*,age_ratings.organization.id,age_ratings.organization.name,age_ratings.rating_category.id,age_ratings.rating_category.rating,age_ratings.rating_content_descriptions.id,age_ratings.rating_content_descriptions.description,
    alternative_names.*,
    keywords.*,
    websites.*,websites.type.id,websites.type.type,
    artworks.*,
    screenshots.*,
    videos.*,
    collections.*,
    franchises.*,
    game_type.*,
    game_status.*,
    multiplayer_modes.*,multiplayer_modes.platform.id,multiplayer_modes.platform.name,
    language_supports.*,language_supports.language.id,language_supports.language.name,language_supports.language.native_name,language_supports.language.locale,language_supports.language_support_type.id,language_supports.language_support_type.name,
    dlcs.id,dlcs.name,dlcs.slug,
    expansions.id,expansions.name,expansions.slug,
    standalone_expansions.id,standalone_expansions.name,standalone_expansions.slug,
    parent_game.id,parent_game.name,parent_game.slug,
    version_parent.id,version_parent.name,version_parent.slug,
    remakes.id,remakes.name,remakes.slug,
    remasters.id,remasters.name,remasters.slug,
    similar_games.id,similar_games.name,similar_games.slug,similar_games.cover.image_id;
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

    /**
     * Bounded title search. The query is embedded in an APICALYPSE `search`
     * clause, so double quotes/backslashes are escaped to keep user input
     * from breaking out of the string literal into the query language.
     *
     * @return list<array<string, mixed>>
     */
    public function search(string $query, int $limit = 12): array
    {
        $escaped = str_replace(['\\', '"'], ['\\\\', '\\"'], $query);
        $limit = max(1, min($limit, 12));

        $games = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(8)
            ->withHeaders([
                'Client-ID' => $this->clientId(),
            ])
            ->withToken($this->accessToken())
            ->withBody(
                "search \"{$escaped}\";\nfields name,cover.image_id,first_release_date,platforms.name;\nlimit {$limit};",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')
            ->throw()
            ->json();

        return is_array($games) ? $games : [];
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
