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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Enums\ModerationStatus;
use App\Models\Apikey;
use App\Models\MediaLanguage;
use App\Models\Subtitle;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('subtitle-files');

    $this->user = User::factory()->create(['can_download' => true]);
    $this->apikey = apiKey($this->user);
    $this->english = MediaLanguage::factory()->create(['name' => 'English', 'code' => 'en']);
    $this->french = MediaLanguage::factory()->create(['name' => 'French', 'code' => 'fr']);
    $this->portuguese = MediaLanguage::factory()->create(['name' => 'Portuguese', 'code' => 'pt']);
    $this->torrent = Torrent::factory()->create([
        'name'          => 'The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP',
        'tmdb_movie_id' => 278,
        'imdb'          => 111161,
        'status'        => ModerationStatus::APPROVED,
    ]);
});

function apiKey(User $user, array $attributes = []): string
{
    return Apikey::query()->create([
        'user_id'       => $user->id,
        'name'          => 'Bazarr',
        'content'       => bin2hex(random_bytes(32)),
        'can_search'    => true,
        'can_download'  => true,
        'can_upload'    => false,
        'can_view_user' => false,
        'expires_at'    => now()->addMonth(),
        'created_at'    => now(),
        ...$attributes,
    ])->content;
}

function apiSubtitle(Torrent $torrent, MediaLanguage $language, array $attributes = []): Subtitle
{
    return Subtitle::factory()->create([
        'torrent_id'  => $torrent->id,
        'language_id' => $language->id,
        'extension'   => '.srt',
        'file_name'   => uniqid('', true).'.srt',
        'downloads'   => 0,
        'status'      => ModerationStatus::APPROVED,
        ...$attributes,
    ]);
}

// Authentication

test('requests without an api key are rejected', function (string $uri): void {
    $this->getJson($uri)->assertUnauthorized();
})->with([
    'status'   => ['api/subtitles/status'],
    'search'   => ['api/subtitles?tmdb_id=278'],
    'download' => ['api/subtitles/1/download'],
]);

test('requests with an invalid api key are rejected', function (): void {
    $this->withToken('not-a-valid-api-key')
        ->getJson('api/subtitles/status')
        ->assertUnauthorized();
});

// Status

test('status reports the provider with a valid api key', function (): void {
    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.status'))
        ->assertOk()
        ->assertExactJson([
            'status'      => 'ok',
            'provider'    => 'unit3d',
            'version'     => config('unit3d.version'),
            'api_version' => 1,
            'permissions' => ['search' => true, 'download' => true],
        ]);
});

test('status reports the permissions of the api key', function (): void {
    $this->withToken(apiKey($this->user, ['can_search' => false, 'can_download' => false]))
        ->getJson(route('api.subtitles.status'))
        ->assertOk()
        ->assertJsonPath('permissions', ['search' => false, 'download' => false]);
});

test('api keys without the search permission cannot search', function (): void {
    apiSubtitle($this->torrent, $this->english);

    $this->withToken(apiKey($this->user, ['can_search' => false]))
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278]))
        ->assertForbidden();
});

test('api keys without the download permission cannot download', function (): void {
    $subtitle = apiSubtitle($this->torrent, $this->english, ['downloads' => 5]);
    Storage::disk('subtitle-files')->put($subtitle->file_name, 'content');

    $this->withToken(apiKey($this->user, ['can_download' => false]))
        ->getJson(route('api.subtitles.download', ['id' => $subtitle->id]))
        ->assertForbidden();

    expect($subtitle->refresh()->downloads)->toBe(5);
});

test('deleted api keys are rejected', function (): void {
    $apikey = apiKey($this->user);
    Apikey::query()->where('content', '=', $apikey)->delete();

    $this->withToken($apikey)
        ->getJson(route('api.subtitles.status'))
        ->assertUnauthorized();
});

// Search

test('search by tmdb id returns only that movie\'s subtitles', function (): void {
    $uploader = User::factory()->create(['username' => 'subber']);
    $subtitle = apiSubtitle($this->torrent, $this->english, ['user_id' => $uploader->id, 'anon' => false]);
    $otherMovie = Torrent::factory()->create(['tmdb_movie_id' => 680, 'imdb' => 110912, 'status' => ModerationStatus::APPROVED]);
    apiSubtitle($otherMovie, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('meta.matched_by', 'tmdb')
        ->assertJsonPath('data.0', [
            'id'               => $subtitle->id,
            'language'         => 'en',
            'language_name'    => 'English',
            'extension'        => 'srt',
            'filename'         => '[English.Subtitle]The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP.srt',
            'size'             => $subtitle->file_size,
            'downloads'        => 0,
            'uploader'         => 'subber',
            'forced'           => null,
            'hearing_impaired' => null,
            'torrent_id'       => $this->torrent->id,
            'release'          => 'The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP',
            'type'             => 'movie',
            'season'           => null,
            'episode'          => null,
            'pack'             => null,
            'tmdb_id'          => 278,
            'tvdb_id'          => null,
            'imdb_id'          => 'tt0111161',
            'created_at'       => $subtitle->created_at->toIso8601String(),
            'download_url'     => '/api/subtitles/'.$subtitle->id.'/download',
        ]);
});

test('search does not expose internal storage or moderation details', function (): void {
    apiSubtitle($this->torrent, $this->english, ['anon' => false, 'note' => 'private note']);

    $response = $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278]))
        ->assertOk();

    expect($response->json('data.0'))
        ->not->toHaveKeys(['file_name', 'user_id', 'user', 'note', 'status', 'moderated_by']);
});

test('anonymous uploaders are not revealed', function (): void {
    $uploader = User::factory()->create(['username' => 'secret-subber']);
    apiSubtitle($this->torrent, $this->english, ['user_id' => $uploader->id, 'anon' => true]);

    $response = $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278]))
        ->assertOk()
        ->assertJsonPath('data.0.uploader', 'Anonymous');

    expect($response->getContent())->not->toContain('secret-subber');
});

test('search by imdb id works when no tmdb id is given', function (string $imdbId): void {
    $subtitle = apiSubtitle($this->torrent, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['imdb_id' => $imdbId]))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'imdb')
        ->assertJsonPath('data.0.id', $subtitle->id);
})->with(['tt0111161', '0111161', '111161']);

test('search falls back to imdb id when the tmdb id matches nothing', function (): void {
    $torrent = Torrent::factory()->create(['tmdb_movie_id' => null, 'imdb' => 133093, 'status' => ModerationStatus::APPROVED]);
    $subtitle = apiSubtitle($torrent, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 603, 'imdb_id' => 'tt0133093']))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'imdb')
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $subtitle->id);
});

test('search does not fall back to imdb id when the tmdb id matches', function (): void {
    $subtitle = apiSubtitle($this->torrent, $this->english);
    $otherMovie = Torrent::factory()->create(['tmdb_movie_id' => 603, 'imdb' => 133093, 'status' => ModerationStatus::APPROVED]);
    apiSubtitle($otherMovie, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278, 'imdb_id' => 'tt0133093']))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'tmdb')
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $subtitle->id);
});

test('search by exact title and year is used when no id is known', function (): void {
    TmdbMovie::factory()->create(['id' => 278, 'title' => 'The Shawshank Redemption', 'release_date' => '1994-09-23']);
    TmdbMovie::factory()->create(['id' => 999278, 'title' => 'The Shawshank Redemption', 'release_date' => '2030-01-01']);
    $subtitle = apiSubtitle($this->torrent, $this->english);
    $remake = Torrent::factory()->create(['tmdb_movie_id' => 999278, 'status' => ModerationStatus::APPROVED]);
    apiSubtitle($remake, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['title' => 'The Shawshank Redemption', 'year' => 1994]))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'title')
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $subtitle->id);
});

test('search by title does not match similar titles', function (): void {
    TmdbMovie::factory()->create(['id' => 278, 'title' => 'The Shawshank Redemption', 'release_date' => '1994-09-23']);
    apiSubtitle($this->torrent, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['title' => 'Shawshank', 'year' => 1994]))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('search filters by language', function (): void {
    apiSubtitle($this->torrent, $this->english);
    $french = apiSubtitle($this->torrent, $this->french);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278, 'language' => 'fr']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $french->id)
        ->assertJsonPath('data.0.language', 'fr');
});

test('search accepts multiple languages', function (): void {
    apiSubtitle($this->torrent, $this->english);
    apiSubtitle($this->torrent, $this->french);
    apiSubtitle($this->torrent, $this->portuguese);

    $response = $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278, 'language' => 'EN,fr']))
        ->assertOk()
        ->assertJsonCount(2, 'data');

    expect(collect($response->json('data'))->pluck('language')->sort()->values()->all())->toBe(['en', 'fr']);
});

test('search results are paginated', function (): void {
    foreach (range(1, 3) as $i) {
        apiSubtitle($this->torrent, $this->english);
    }

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278, 'perPage' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.total', 3)
        ->assertJsonPath('meta.per_page', 2);
});

test('search validates its parameters', function (array $query): void {
    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', $query))
        ->assertUnprocessable();
})->with([
    'no movie identifier'    => [[]],
    'only a language'        => [['language' => 'en']],
    'title without year'     => [['title' => 'The Shawshank Redemption']],
    'non numeric tmdb id'    => [['tmdb_id' => '278 OR 1=1']],
    'malformed imdb id'      => [['imdb_id' => '../../etc/passwd']],
    'zero imdb id'           => [['imdb_id' => 'tt0000000']],
    'zero tmdb id'           => [['tmdb_id' => 0]],
    'three letter language'  => [['tmdb_id' => 278, 'language' => 'eng']],
    'sql in language'        => [['tmdb_id' => 278, 'language' => "en') OR ('1'='1"]],
    'too many per page'      => [['tmdb_id' => 278, 'perPage' => 51]],
    'episode without season' => [['type' => 'episode', 'tmdb_id' => 1396, 'episode' => 5]],
    'episode without number' => [['type' => 'episode', 'tmdb_id' => 1396, 'season' => 1]],
    'season for a movie'     => [['tmdb_id' => 278, 'season' => 1, 'episode' => 1]],
    'tvdb id for a movie'    => [['tvdb_id' => 81189]],
    'unknown type'           => [['type' => 'series', 'tmdb_id' => 1396]],
]);

// Moderation

test('search only returns approved subtitles of approved torrents', function (): void {
    $approved = apiSubtitle($this->torrent, $this->english);
    apiSubtitle($this->torrent, $this->english, ['status' => ModerationStatus::PENDING]);
    apiSubtitle($this->torrent, $this->english, ['status' => ModerationStatus::REJECTED]);
    apiSubtitle($this->torrent, $this->english, ['status' => ModerationStatus::POSTPONED]);

    $pendingTorrent = Torrent::factory()->create(['tmdb_movie_id' => 278, 'status' => ModerationStatus::PENDING]);
    apiSubtitle($pendingTorrent, $this->english);

    $deletedTorrent = Torrent::factory()->create(['tmdb_movie_id' => 278, 'status' => ModerationStatus::APPROVED]);
    apiSubtitle($deletedTorrent, $this->english);
    $deletedTorrent->delete();

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['tmdb_id' => 278]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $approved->id);
});

// Download

test('an approved subtitle can be downloaded and its download is counted', function (): void {
    $subtitle = apiSubtitle($this->torrent, $this->english, ['downloads' => 5]);
    Storage::disk('subtitle-files')->put($subtitle->file_name, "1\n00:00:01,000 --> 00:00:02,000\nHello\n");

    $response = $this->withToken($this->apikey)
        ->get(route('api.subtitles.download', ['id' => $subtitle->id]), ['Accept' => 'application/json'])
        ->assertOk()
        ->assertDownload('[English.Subtitle]The.Shawshank.Redemption.1994.1080p.BluRay.x264-GRP.srt');

    expect($response->streamedContent())->toBe("1\n00:00:01,000 --> 00:00:02,000\nHello\n")
        ->and($subtitle->refresh()->downloads)->toBe(6);
});

test('a missing subtitle file returns not found without counting a download', function (): void {
    $subtitle = apiSubtitle($this->torrent, $this->english, ['downloads' => 5]);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.download', ['id' => $subtitle->id]))
        ->assertNotFound();

    expect($subtitle->refresh()->downloads)->toBe(5);
});

test('unapproved subtitles cannot be downloaded by id', function (ModerationStatus $status): void {
    $subtitle = apiSubtitle($this->torrent, $this->english, ['status' => $status]);
    Storage::disk('subtitle-files')->put($subtitle->file_name, 'content');

    $this->withToken($this->apikey)
        ->getJson('api/subtitles/'.$subtitle->id.'/download')
        ->assertNotFound();
})->with([ModerationStatus::PENDING, ModerationStatus::REJECTED, ModerationStatus::POSTPONED]);

test('subtitles of unapproved torrents cannot be downloaded by id', function (): void {
    $pendingTorrent = Torrent::factory()->create(['status' => ModerationStatus::PENDING]);
    $subtitle = apiSubtitle($pendingTorrent, $this->english);
    Storage::disk('subtitle-files')->put($subtitle->file_name, 'content');

    $this->withToken($this->apikey)
        ->getJson('api/subtitles/'.$subtitle->id.'/download')
        ->assertNotFound();
});

test('users with revoked download rights cannot download', function (): void {
    $user = User::factory()->create(['can_download' => false]);
    $subtitle = apiSubtitle($this->torrent, $this->english, ['downloads' => 5]);
    Storage::disk('subtitle-files')->put($subtitle->file_name, 'content');

    $this->withToken(apiKey($user))
        ->getJson(route('api.subtitles.download', ['id' => $subtitle->id]))
        ->assertForbidden();

    expect($subtitle->refresh()->downloads)->toBe(5);
});

test('the download route only accepts numeric subtitle ids', function (): void {
    $this->withToken($this->apikey)
        ->getJson('api/subtitles/..%2F..%2F.env/download')
        ->assertNotFound();
});

// TV episodes

function showTorrent(int $season, int $episode, array $attributes = []): Torrent
{
    return Torrent::factory()->create([
        'name'           => \sprintf('Breaking.Bad.S%02dE%02d.1080p.WEB-DL.x264-GRP', $season, $episode),
        'tmdb_movie_id'  => null,
        'tmdb_tv_id'     => 1396,
        'tvdb'           => 81189,
        'imdb'           => 903747,
        'season_number'  => $season,
        'episode_number' => $episode,
        'status'         => ModerationStatus::APPROVED,
        ...$attributes,
    ]);
}

test('episode search returns the episode, its season pack and complete series packs', function (): void {
    $episode = apiSubtitle(showTorrent(1, 5), $this->english);
    $seasonPack = apiSubtitle(showTorrent(1, 0), $this->english, ['extension' => '.zip']);
    $seriesPack = apiSubtitle(showTorrent(0, 0), $this->english, ['extension' => '.zip']);
    apiSubtitle(showTorrent(1, 6), $this->english);
    apiSubtitle(showTorrent(2, 0), $this->english);
    apiSubtitle(showTorrent(2, 5), $this->english);
    apiSubtitle(showTorrent(1, 5, ['tmdb_tv_id' => 1399, 'tvdb' => 121361, 'imdb' => 944947]), $this->english);

    $response = $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'tmdb_id' => 1396, 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.matched_by', 'tmdb');

    $results = collect($response->json('data'))->keyBy('id');

    expect($results->keys()->sort()->values()->all())->toBe(collect([$episode->id, $seasonPack->id, $seriesPack->id])->sort()->values()->all())
        ->and($results[$episode->id])->toMatchArray([
            'type'    => 'episode',
            'season'  => 1,
            'episode' => 5,
            'pack'    => null,
            'tmdb_id' => 1396,
            'tvdb_id' => 81189,
            'imdb_id' => 'tt0903747',
            'release' => 'Breaking.Bad.S01E05.1080p.WEB-DL.x264-GRP',
        ])
        ->and($results[$seasonPack->id])->toMatchArray(['season' => 1, 'episode' => null, 'pack' => 'season'])
        ->and($results[$seriesPack->id])->toMatchArray(['season' => null, 'episode' => null, 'pack' => 'series']);
});

test('episode search by tvdb id', function (): void {
    $subtitle = apiSubtitle(showTorrent(1, 5, ['tmdb_tv_id' => null]), $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'tvdb_id' => 81189, 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'tvdb')
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $subtitle->id);
});

test('episode search falls back from tvdb to imdb id', function (): void {
    $subtitle = apiSubtitle(showTorrent(1, 5, ['tmdb_tv_id' => null, 'tvdb' => null]), $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'tvdb_id' => 81189, 'imdb_id' => 'tt0903747', 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'imdb')
        ->assertJsonPath('data.0.id', $subtitle->id);
});

test('episode search by exact show name and first air year', function (): void {
    TmdbTv::factory()->create(['id' => 1396, 'name' => 'Breaking Bad', 'first_air_date' => '2008-01-20', 'last_air_date' => '2013-09-29']);
    $subtitle = apiSubtitle(showTorrent(1, 5), $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'title' => 'Breaking Bad', 'year' => 2008, 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonPath('meta.matched_by', 'title')
        ->assertJsonPath('data.0.id', $subtitle->id);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'title' => 'Breaking Bad', 'year' => 2009, 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('special episodes match season zero', function (): void {
    $special = apiSubtitle(showTorrent(0, 2), $this->english);
    $seriesPack = apiSubtitle(showTorrent(0, 0), $this->english, ['extension' => '.zip']);
    apiSubtitle(showTorrent(0, 3), $this->english);

    $response = $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'tmdb_id' => 1396, 'season' => 0, 'episode' => 2]))
        ->assertOk()
        ->assertJsonCount(2, 'data');

    expect(collect($response->json('data'))->pluck('id')->sort()->values()->all())
        ->toBe(collect([$special->id, $seriesPack->id])->sort()->values()->all());
});

test('movie and episode searches do not mix', function (): void {
    apiSubtitle(showTorrent(1, 5, ['imdb' => 111161]), $this->english);
    $movie = apiSubtitle($this->torrent, $this->english);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['imdb_id' => 'tt0111161']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $movie->id);

    $this->withToken($this->apikey)
        ->getJson(route('api.subtitles.index', ['type' => 'episode', 'tmdb_id' => 278, 'season' => 1, 'episode' => 5]))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
