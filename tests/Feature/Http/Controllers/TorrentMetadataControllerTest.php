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

// A well-known, checksum-valid ISBN-13 (978-0-306-40615-7).
const VALID_ISBN_13 = '9780306406157';

function bookCategory(): Category
{
    return Category::factory()->create([
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'book_meta'  => true,
        'no_meta'    => false,
    ]);
}

function uploaderUser(): User
{
    return User::factory()->create(['can_upload' => true]);
}

test('non uploader is forbidden', function (): void {
    $category = bookCategory();
    $user = User::factory()->create(['can_upload' => false]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => VALID_ISBN_13,
    ]));

    $response->assertForbidden();
});

test('xxx and no meta categories reject lookup without any outbound http calls', function (): void {
    Http::fake();

    $category = Category::factory()->create([
        'name'       => 'XXX',
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'book_meta'  => false,
        'no_meta'    => false,
    ]);
    $user = uploaderUser();

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => '12345',
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('invalid isbn checksum is rejected without any outbound http calls', function (): void {
    Http::fake();

    $category = bookCategory();
    $user = uploaderUser();

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => '9780306406158', // valid format, invalid checksum
    ]));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('a non czech google books match is never surfaced as a czech description', function (): void {
    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [
                [
                    'volumeInfo' => [
                        'title'               => 'Example Title',
                        'description'         => 'An English description.',
                        'language'            => 'en',
                        'industryIdentifiers' => [
                            ['type' => 'ISBN_13', 'identifier' => VALID_ISBN_13],
                        ],
                    ],
                ],
            ],
        ], 200),
        'https://openlibrary.org/*' => Http::response([], 404),
    ]);

    $category = bookCategory();
    $user = uploaderUser();

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => VALID_ISBN_13,
    ]));

    $response->assertOk();
    expect($response->json('description_language'))->not->toBe('cs');
    expect($response->json('warning'))->not->toBeNull();
});

test('an exact czech language google books match is used as the czech description', function (): void {
    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [
                [
                    'volumeInfo' => [
                        'title'               => 'Příklad',
                        'description'         => 'Český popis knihy.',
                        'language'            => 'cs',
                        'industryIdentifiers' => [
                            ['type' => 'ISBN_13', 'identifier' => VALID_ISBN_13],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $category = bookCategory();
    $user = uploaderUser();

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => VALID_ISBN_13,
    ]));

    $response->assertOk();
    $response->assertJson([
        'description_language' => 'cs',
        'description'           => 'Český popis knihy.',
        'warning'               => null,
    ]);
    Http::assertSentCount(1);
});

test('an unrelated isbn result is rejected rather than filling the wrong book', function (): void {
    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => [[
            'volumeInfo' => [
                'title' => 'Jiná kniha', 'description' => 'Popis jiné knihy.', 'language' => 'cs',
                'industryIdentifiers' => [['type' => 'ISBN_13', 'identifier' => '9788071970199']],
            ],
        ]]], 200),
        'https://openlibrary.org/*' => Http::response([], 404),
    ]);
    $this->actingAs(uploaderUser())->getJson(route('torrents.metadata', [
        'category_id' => bookCategory()->id, 'identifier' => VALID_ISBN_13,
    ]))->assertNotFound();
});

test('a czech book without annotation keeps a missing description warning', function (): void {
    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => [[
            'volumeInfo' => [
                'title' => 'Kniha bez anotace', 'language' => 'cs',
                'industryIdentifiers' => [['type' => 'ISBN_13', 'identifier' => VALID_ISBN_13]],
            ],
        ]]], 200),
        'https://openlibrary.org/*' => Http::response([], 404),
    ]);
    $response = $this->actingAs(uploaderUser())->getJson(route('torrents.metadata', [
        'category_id' => bookCategory()->id, 'identifier' => VALID_ISBN_13,
    ]));
    $response->assertOk();
    expect($response->json('description'))->toBe('');
    expect($response->json('description_language'))->toBeNull();
    expect($response->json('warning'))->not->toBeNull();
});

test('no verified czech description anywhere reports a warning instead of fabricating one', function (): void {
    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []], 200),
        'https://openlibrary.org/isbn/*'                => Http::response([
            'key'   => '/books/OL1M',
            'title' => 'Some Book',
        ], 200),
    ]);

    $category = bookCategory();
    $user = uploaderUser();

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', [
        'category_id' => $category->id,
        'identifier'  => VALID_ISBN_13,
    ]));

    $response->assertOk();
    expect($response->json('description_language'))->toBeNull();
    expect($response->json('warning'))->not->toBeNull();
});

test('movie lookup reports czech overview with no warning when czech overview exists', function (): void {
    config(['api-keys.tmdb' => 'test-key']);

    $movieResponse = [
        'id'           => 550,
        'title'        => 'Klub rváčů',
        'overview'     => 'Český popis.',
        'poster_path'  => '/poster.jpg',
        'release_date' => '1999-10-15',
        'external_ids' => ['imdb_id' => 'tt0137523'],
    ];

    Http::fake([
        'https://api.TheMovieDB.org/3/movie/550*' => Http::response($movieResponse, 200),
        'https://api.themoviedb.org/3/movie/550*' => Http::response($movieResponse, 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => true, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '550']));

    $response->assertOk();
    $response->assertJson([
        'title'                => 'Klub rváčů',
        'description'          => 'Český popis.',
        'description_language' => 'cs',
        'warning'              => null,
        'identifiers'          => ['tmdb_movie_id' => '550'],
    ]);
});

test('movie lookup warns when overview is english fallback', function (): void {
    config(['api-keys.tmdb' => 'test-key']);

    // Localized (cs-CZ) request returns blank overview -> client merges English fallback silently.
    Http::fake(function (\Illuminate\Http\Client\Request $request) {
        $language = $request->data()['language'] ?? null;

        return match ($language) {
            'cs-CZ' => Http::response(['id' => 550, 'title' => 'Fight Club', 'overview' => '', 'poster_path' => null, 'release_date' => '1999-10-15', 'external_ids' => []], 200),
            'en-US' => Http::response(['id' => 550, 'title' => 'Fight Club', 'overview' => 'English overview.', 'poster_path' => null, 'release_date' => '1999-10-15', 'external_ids' => []], 200),
            default => Http::response(['overview' => ''], 200),
        };
    });

    $category = Category::factory()->create(['movie_meta' => true, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '550']));

    $response->assertOk();
    expect($response->json('description'))->toBe('English overview.');
    expect($response->json('description_language'))->toBe('en');
    expect($response->json('warning'))->not->toBeNull();
});

test('game lookup reports 503 when igdb credentials are not configured', function (): void {
    config(['igdb.credentials.client_id' => '', 'igdb.credentials.client_secret' => '']);
    Http::fake();

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => true, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '1942']));

    $response->assertStatus(503);
    Http::assertNothingSent();
});

test('game lookup succeeds against a fully mocked igdb + twitch flow', function (): void {
    config(['igdb.credentials.client_id' => 'cid', 'igdb.credentials.client_secret' => 'secret']);

    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600], 200),
        'https://api.igdb.com/v4/games' => Http::response([
            ['id' => 1942, 'name' => 'Sample Game', 'summary' => 'A summary.', 'url' => 'https://igdb.com/games/sample', 'cover' => ['image_id' => 'abc123']],
        ], 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => true, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '1942']));

    $response->assertOk();
    $response->assertJson([
        'title'       => 'Sample Game',
        'description' => 'A summary.',
        'source'      => 'IGDB',
        'identifiers' => ['igdb' => '1942'],
    ]);
});

test('game lookup returns 404 for an igdb id that does not exist', function (): void {
    config(['igdb.credentials.client_id' => 'cid', 'igdb.credentials.client_secret' => 'secret']);

    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600], 200),
        'https://api.igdb.com/v4/games' => Http::response([], 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => true, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '999999']));

    $response->assertStatus(404);
});

test('music lookup rejects a non uuid identifier without any outbound http calls', function (): void {
    Http::fake();

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => true, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => 'not-a-uuid']));

    $response->assertStatus(422);
    Http::assertNothingSent();
});

test('music lookup succeeds against a mocked musicbrainz release', function (): void {
    $mbid = '76df3287-6cda-33eb-8e9a-044b5e15ffdd';

    Http::fake([
        "https://musicbrainz.org/ws/2/release/{$mbid}*" => Http::response([
            'id' => $mbid, 'title' => 'Sample Release', 'artist-credit' => [['name' => 'Sample Artist']],
        ], 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => true, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => $mbid]));

    $response->assertOk();
    $response->assertJson([
        'title'       => 'Sample Artist - Sample Release',
        'source'      => 'MusicBrainz',
        'identifiers' => ['musicbrainz_id' => $mbid],
    ]);
});

test('music lookup accepts an album id without selecting an arbitrary release', function (): void {
    $mbid = 'df4b48f5-77fe-499d-a6a2-ca83d186beb8';
    $album = [
        'id' => $mbid, 'title' => 'Mockingbird', 'primary-type' => 'Single',
        'first-release-date' => '2005-04-25',
        'artist-credit' => [['name' => 'Eminem']],
        'genres' => [['name' => 'hip hop']],
    ];
    Http::fake([
        "https://musicbrainz.org/ws/2/release/{$mbid}*" => Http::response(['error' => 'Not Found'], 404),
        "https://musicbrainz.org/ws/2/release-group/{$mbid}*" => Http::response($album, 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => true, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);
    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => $mbid]));

    $response->assertOk()->assertJsonPath('title', 'Eminem - Mockingbird')
        ->assertJsonPath('source_url', "https://musicbrainz.org/release-group/{$mbid}")
        ->assertJsonPath('cover_url', "https://coverartarchive.org/release-group/{$mbid}/front")
        ->assertJsonPath('identifiers.musicbrainz_id', $mbid)
        ->assertJsonPath('raw', $album);
    $values = collect($response->json('details'))->pluck('value', 'key');
    expect($values->get('release_date'))->toBe('2005-04-25')
        ->and($values->get('genres'))->toBe('hip hop')
        ->and($values->has('labels'))->toBeFalse()
        ->and($values->has('tracklist'))->toBeFalse();
});

test('music lookup preserves provider outages instead of treating them as album ids', function (): void {
    $mbid = 'df4b48f5-77fe-499d-a6a2-ca83d186beb8';
    Http::fake([
        "https://musicbrainz.org/ws/2/release/{$mbid}*" => Http::response([], 503),
    ]);
    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => true, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => $mbid]))->assertStatus(503);
    Http::assertSentCount(1);
});

test('music lookup returns not found when neither a release nor an album exists', function (): void {
    Http::fake(['https://musicbrainz.org/ws/2/*' => Http::response(['error' => 'Not Found'], 404)]);
    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => false, 'music_meta' => true, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => 'df4b48f5-77fe-499d-a6a2-ca83d186beb8']))->assertStatus(404);
});

test('a successful lookup carries a selection token binding the trusted result to this user and category', function (): void {
    config(['igdb.credentials.client_id' => 'cid', 'igdb.credentials.client_secret' => 'secret']);

    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600], 200),
        'https://api.igdb.com/v4/games' => Http::response([
            ['id' => 1942, 'name' => 'Sample Game', 'summary' => 'A summary.', 'cover' => ['image_id' => 'abc123']],
        ], 200),
    ]);

    $category = Category::factory()->create(['movie_meta' => false, 'tv_meta' => false, 'game_meta' => true, 'music_meta' => false, 'book_meta' => false, 'no_meta' => false]);
    $user = User::factory()->create(['can_upload' => true]);

    $response = $this->actingAs($user)->getJson(route('torrents.metadata', ['category_id' => $category->id, 'identifier' => '1942']));

    $response->assertOk();
    $token = $response->json('selection_token');
    expect($token)->toBeString()->not->toBeEmpty();

    $retrieved = app(App\Services\Metadata\MetadataSelectionStore::class)->retrieve($user, $category, $token);
    expect($retrieved['identifiers']['igdb'])->toBe('1942');

    // The token is bound to this user/category, not usable by another.
    $otherUser = User::factory()->create(['can_upload' => true]);
    expect(fn () => app(App\Services\Metadata\MetadataSelectionStore::class)->retrieve($otherUser, $category, $token))
        ->toThrow(Illuminate\Validation\ValidationException::class);
});
