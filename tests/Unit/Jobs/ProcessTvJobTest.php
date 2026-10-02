<?php

declare(strict_types=1);

use App\Jobs\ProcessTvJob;
use App\Models\TmdbTv;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    cache()->flush();
});

function fakeTvPayload(string $summaryMarker, array $extra = []): array
{
    return array_merge([
        'id'                   => 1399,
        'name'                 => 'Game of Thrones',
        'external_ids'         => ['imdb_id' => 'tt0944947', 'tvdb_id' => 121361],
        'genres'               => [],
        'production_companies' => [],
        'networks'             => [],
        'aggregate_credits'    => ['cast' => [], 'crew' => []],
        'created_by'           => [],
        'recommendations'      => ['results' => []],
        'videos'               => ['results' => []],
        'marker'               => $summaryMarker,
    ], $extra);
}

it('stores the entire raw TMDB TV payload alongside the mapped columns', function (): void {
    Http::fake([
        'https://api.TheMovieDB.org/3/tv/1399*' => Http::response(fakeTvPayload('first-fetch', [
            'content_ratings' => ['results' => [['iso_3166_1' => 'US', 'rating' => 'TV-MA']]],
        ])),
        'https://api.themoviedb.org/3/tv/1399*' => Http::response(fakeTvPayload('first-fetch', [
            'content_ratings' => ['results' => [['iso_3166_1' => 'US', 'rating' => 'TV-MA']]],
        ])),
    ]);

    (new ProcessTvJob(1399))->handle();

    $tv = TmdbTv::query()->findOrFail(1399);

    expect($tv->raw['marker'])->toBe('first-fetch')
        ->and($tv->raw['content_ratings']['results'][0]['rating'])->toBe('TV-MA');
});

it('replaces stale raw fields when the tv show is refetched', function (): void {
    Http::fake([
        'https://api.TheMovieDB.org/3/tv/1399*' => Http::sequence()
            ->push(fakeTvPayload('first-fetch', [
                'content_ratings' => ['results' => [['iso_3166_1' => 'US']]],
            ]))
            ->push(fakeTvPayload('second-fetch')),
        'https://api.themoviedb.org/3/tv/1399*' => Http::sequence()
            ->push(fakeTvPayload('first-fetch', [
                'content_ratings' => ['results' => [['iso_3166_1' => 'US']]],
            ]))
            ->push(fakeTvPayload('second-fetch')),
    ]);

    (new ProcessTvJob(1399))->handle();

    cache()->flush();

    (new ProcessTvJob(1399))->handle();

    $tv = TmdbTv::query()->findOrFail(1399);

    expect($tv->raw)->not->toHaveKey('content_ratings')
        ->and($tv->raw['marker'])->toBe('second-fetch');
});
