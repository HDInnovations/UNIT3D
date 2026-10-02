<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function discoverySearchCategory(string $kind): Category
{
    return Category::factory()->create([
        'name'       => $kind === 'unsupported' ? 'Unsupported' : ucfirst($kind),
        'movie_meta' => $kind === 'movie',
        'tv_meta'    => $kind === 'tv',
        'game_meta'  => $kind === 'game',
        'music_meta' => $kind === 'music',
        'book_meta'  => $kind === 'book',
        'no_meta'    => false,
    ]);
}

function discoveryUploader(): User
{
    return User::factory()->create(['can_upload' => true]);
}

const DISCOVERY_MBID = '76df3287-6cda-33eb-8e9a-044b5e15ffdd';

test('non uploader is forbidden from title search', function (): void {
    $category = discoverySearchCategory('movie');
    $user = User::factory()->create(['can_upload' => false]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata.search', [
        'category_id' => $category->id, 'query' => 'matrix',
    ]));

    $response->assertForbidden();
});

test('non uploader is forbidden from music release pagination', function (): void {
    $category = discoverySearchCategory('music');
    $user = User::factory()->create(['can_upload' => false]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata.music-releases', [
        'category_id' => $category->id, 'identifier' => DISCOVERY_MBID,
    ]));

    $response->assertForbidden();
});

test('a query shorter than two characters is rejected without any outbound http calls', function (): void {
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('movie')->id, 'query' => 'a',
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('a category without a searchable kind is rejected without any outbound http calls', function (): void {
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('unsupported')->id, 'query' => 'matrix',
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('movie search reports provider unavailable, not an empty result, when tmdb is not configured', function (): void {
    config(['api-keys.tmdb' => '']);
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('movie')->id, 'query' => 'matrix',
    ]));

    $response->assertStatus(503);
    Http::assertNothingSent();
});

test('movie search results are bounded to twelve even when the provider returns more', function (): void {
    config(['api-keys.tmdb' => 'test-key']);

    $results = collect(range(1, 20))->map(fn (int $i): array => [
        'id' => $i, 'title' => "Movie {$i}", 'release_date' => '2000-01-01',
    ])->all();

    Http::fake([
        'https://api.themoviedb.org/3/search/movie*' => Http::response(['results' => $results], 200),
    ]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('movie')->id, 'query' => 'movie',
    ]));

    $response->assertOk();
    expect($response->json('results'))->toHaveCount(12);
});

test('tv search returns real candidates with tmdb tv identity, never a movie', function (): void {
    config(['api-keys.tmdb' => 'test-key']);

    Http::fake([
        'https://api.themoviedb.org/3/search/tv*' => Http::response(['results' => [
            ['id' => 1399, 'name' => 'Game of Thrones', 'original_name' => 'Game of Thrones', 'first_air_date' => '2011-04-17', 'poster_path' => '/got.jpg'],
        ]], 200),
    ]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('tv')->id, 'query' => 'thrones',
    ]));

    $response->assertOk();
    expect($response->json('results.0'))->toMatchArray([
        'identifier' => '1399', 'entity' => 'tv', 'title' => 'Game of Thrones', 'year' => '2011',
    ]);
});

test('game search reuses the existing twitch client credentials flow for a token', function (): void {
    config(['igdb.credentials.client_id' => 'cid', 'igdb.credentials.client_secret' => 'secret']);

    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600], 200),
        'https://api.igdb.com/v4/games' => Http::response([
            ['id' => 1942, 'name' => 'Sample Game', 'first_release_date' => 946684800, 'cover' => ['image_id' => 'abc123']],
        ], 200),
    ]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('game')->id, 'query' => 'sample',
    ]));

    $response->assertOk();
    expect($response->json('results.0'))->toMatchArray([
        'identifier' => '1942', 'entity' => 'game', 'title' => 'Sample Game', 'year' => '2000',
    ]);

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://id.twitch.tv/oauth2/token'
            && ($request['client_id'] ?? null) === 'cid'
            && ($request['grant_type'] ?? null) === 'client_credentials';
    });
});

test('game search reports provider unavailable when igdb credentials are missing', function (): void {
    config(['igdb.credentials.client_id' => '', 'igdb.credentials.client_secret' => '']);
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('game')->id, 'query' => 'sample',
    ]));

    $response->assertStatus(503);
    Http::assertNothingSent();
});

test('music search escapes lucene special characters so a query cannot manipulate the musicbrainz search syntax', function (): void {
    Http::fake(['https://musicbrainz.org/ws/2/release-group/*' => Http::response(['release-groups' => []], 200)]);

    $malicious = 'test") OR (artist:*';

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('music')->id, 'query' => $malicious,
    ]));

    $response->assertOk();
    expect($response->json('results'))->toBe([]);

    Http::assertSent(function ($request) use ($malicious): bool {
        $sentQuery = $request->data()['query'] ?? null;

        return \is_string($sentQuery)
            && $sentQuery !== $malicious
            && !str_contains($sentQuery, ') OR (')
            && str_contains($sentQuery, '\\(')
            && str_contains($sentQuery, '\\)')
            && str_contains($sentQuery, '\\*');
    });
});

test('book search never returns an open library work id as a usable choice', function (): void {
    Http::fake(['https://openlibrary.org/search.json*' => Http::response(['docs' => [
        ['title' => 'Work without any edition', 'author_name' => ['Someone']],
        ['title' => 'Work with an edition', 'author_name' => ['Someone Else'], 'edition_key' => ['OL7353617M'], 'first_publish_year' => 1990],
    ]], 200)]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('book')->id, 'query' => 'sample book',
    ]));

    $response->assertOk();
    $results = $response->json('results');
    expect($results)->toHaveCount(1)
        ->and($results[0]['identifier'])->toBe('OL7353617M')
        ->and($results[0]['entity'])->toBe('book');
});

test('book search escapes lucene special characters before querying open library', function (): void {
    Http::fake(['https://openlibrary.org/search.json*' => Http::response(['docs' => []], 200)]);

    $malicious = 'title:"anything" OR *:*';

    $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.search', [
        'category_id' => discoverySearchCategory('book')->id, 'query' => $malicious,
    ]))->assertOk();

    Http::assertSent(function ($request) use ($malicious): bool {
        $sentQuery = $request->data()['q'] ?? null;

        return \is_string($sentQuery) && $sentQuery !== $malicious && !str_contains($sentQuery, '"anything"');
    });
});

test('music releases requires a music category', function (): void {
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('movie')->id, 'identifier' => DISCOVERY_MBID,
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('music releases rejects a non uuid identifier without any outbound http calls', function (): void {
    Http::fake();

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('music')->id, 'identifier' => 'not-a-uuid',
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('music releases page is bounded and reports the next offset when editions remain', function (): void {
    $releases = collect(range(1, 12))->map(fn (int $i): array => [
        'id' => "release-{$i}", 'title' => "Edition {$i}", 'date' => '1999-01-01', 'country' => 'GB',
        'artist-credit' => [['name' => 'Sample Artist']],
        'media' => [['format' => 'CD', 'track-count' => 10]],
    ])->all();

    Http::fake(['https://musicbrainz.org/ws/2/release/*' => Http::response([
        'release-count' => 30, 'releases' => $releases,
    ], 200)]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('music')->id, 'identifier' => DISCOVERY_MBID, 'offset' => 0,
    ]));

    $response->assertOk();
    expect($response->json('results'))->toHaveCount(12)
        ->and($response->json('next_offset'))->toBe(12)
        ->and($response->json('results.0'))->toMatchArray([
            'identifier' => 'release-1', 'entity' => 'release', 'title' => 'Edition 1',
            'year' => '1999', 'artists' => 'Sample Artist', 'country' => 'GB',
            'format' => 'CD', 'track_count' => 10,
        ]);
});

test('music releases reports no next offset on the last page', function (): void {
    Http::fake(['https://musicbrainz.org/ws/2/release/*' => Http::response([
        'release-count' => 3,
        'releases'      => collect(range(1, 3))->map(fn (int $i): array => ['id' => "r{$i}", 'title' => "R{$i}"])->all(),
    ], 200)]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('music')->id, 'identifier' => DISCOVERY_MBID, 'offset' => 0,
    ]));

    $response->assertOk();
    expect($response->json('next_offset'))->toBeNull();
});

test('music releases distinguishes a provider outage from an unknown release group', function (): void {
    Http::fake(['https://musicbrainz.org/ws/2/release/*' => Http::response([], 503)]);

    $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('music')->id, 'identifier' => DISCOVERY_MBID,
    ]))->assertStatus(503);
});

test('music releases returns an empty page rather than an error for an unknown release group', function (): void {
    Http::fake(['https://musicbrainz.org/ws/2/release/*' => Http::response(['error' => 'Not Found'], 404)]);

    $response = $this->actingAs(discoveryUploader())->getJson(route('torrents.metadata.music-releases', [
        'category_id' => discoverySearchCategory('music')->id, 'identifier' => DISCOVERY_MBID,
    ]));

    $response->assertOk();
    expect($response->json('results'))->toBe([]);
    expect($response->json('next_offset'))->toBeNull();
});
