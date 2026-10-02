<?php

declare(strict_types=1);

use App\Jobs\ProcessMovieJob;
use App\Models\TmdbMovie;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    cache()->flush();
});

function fakeMoviePayload(string $summaryMarker, array $extra = []): array
{
    return array_merge([
        'id'                    => 550,
        'title'                 => 'Fight Club',
        'release_date'          => '1999-10-15',
        'imdb_id'               => 'tt0137523',
        'genres'                => [],
        'production_companies'  => [],
        'belongs_to_collection' => null,
        'credits'               => ['cast' => [], 'crew' => []],
        'recommendations'       => ['results' => []],
        'videos'                => ['results' => []],
        'marker'                => $summaryMarker,
    ], $extra);
}

it('stores the entire raw TMDB movie payload alongside the mapped columns', function (): void {
    Http::fake([
        'https://api.TheMovieDB.org/3/movie/550*' => Http::response(fakeMoviePayload('first-fetch', [
            'release_dates' => ['results' => [['iso_3166_1' => 'US', 'release_dates' => [['certification' => 'R']]]]],
        ])),
        'https://api.themoviedb.org/3/movie/550*' => Http::response(fakeMoviePayload('first-fetch', [
            'release_dates' => ['results' => [['iso_3166_1' => 'US', 'release_dates' => [['certification' => 'R']]]]],
        ])),
    ]);

    (new ProcessMovieJob(550))->handle();

    $movie = TmdbMovie::query()->findOrFail(550);

    expect($movie->raw['marker'])->toBe('first-fetch')
        ->and($movie->raw['release_dates']['results'][0]['iso_3166_1'])->toBe('US');
});

it('replaces stale raw fields when the movie is refetched', function (): void {
    Http::fake([
        'https://api.TheMovieDB.org/3/movie/550*' => Http::sequence()
            ->push(fakeMoviePayload('first-fetch', [
                'release_dates' => ['results' => [['iso_3166_1' => 'US']]],
            ]))
            ->push(fakeMoviePayload('second-fetch')),
        'https://api.themoviedb.org/3/movie/550*' => Http::sequence()
            ->push(fakeMoviePayload('first-fetch', [
                'release_dates' => ['results' => [['iso_3166_1' => 'US']]],
            ]))
            ->push(fakeMoviePayload('second-fetch')),
    ]);

    (new ProcessMovieJob(550))->handle();

    cache()->flush();

    (new ProcessMovieJob(550))->handle();

    $movie = TmdbMovie::query()->findOrFail(550);

    expect($movie->raw)->not->toHaveKey('release_dates')
        ->and($movie->raw['marker'])->toBe('second-fetch');
});
