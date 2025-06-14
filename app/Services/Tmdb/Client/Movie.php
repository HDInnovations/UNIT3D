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

namespace App\Services\Tmdb\Client;

use App\Enums\Occupation;
use App\Exceptions\MetaFetchNotFoundException;
use App\Services\Tmdb\TMDB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use DateTime;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class Movie
{
    /**
     * @var null|array{
     *     adult: ?bool,
     *     backdrop_path: ?string,
     *     belongs_to_collection: ?array{
     *         id: int,
     *         name: ?string,
     *         poster_path: ?string,
     *         backdrop_path: ?string,
     *     },
     *     budget: ?int,
     *     genres: ?array<
     *         int<0, max>,
     *         array{
     *             id: ?int,
     *             name: ?string,
     *         },
     *     >,
     *     homepage: ?string,
     *     id: ?int,
     *     imdb_id: ?string,
     *     original_language: ?string,
     *     original_title: ?string,
     *     overview: ?string,
     *     popularity: ?float,
     *     poster_path: ?string,
     *     production_companies: ?array<
     *         int,
     *         array{
     *             id: int,
     *             logo_path: ?string,
     *             name: ?string,
     *             origin_country: ?string,
     *         },
     *     >,
     *     production_countries: ?array<
     *         int<0, max>,
     *         array{
     *             iso_3166_1: ?string,
     *             name: ?string,
     *         },
     *     >,
     *     release_date: ?string,
     *     revenue: ?int,
     *     runtime: ?int,
     *     spoken_languages: ?array<
     *         int<0, max>,
     *         array{
     *             english_name: ?string,
     *             iso_639_1: ?string,
     *             name: ?string,
     *         },
     *     >,
     *     status: ?string,
     *     tagline: ?string,
     *     title: ?string,
     *     vote_average: ?float,
     *     vote_count: ?int,
     *     credits: ?array{
     *         id: ?int,
     *         cast: ?array<
     *             int<0, max>,
     *             array{
     *                 adult: ?bool,
     *                 gender: ?int,
     *                 id: ?int,
     *                 known_for_department: ?string,
     *                 name: ?string,
     *                 original_name: ?string,
     *                 popularity: ?float,
     *                 profile_path: ?string,
     *                 cast_id: ?int,
     *                 character: ?string,
     *                 credit_id: ?string,
     *                 order: ?int,
     *             },
     *         >,
     *         crew: ?array<
     *             int<0, max>,
     *             array{
     *                 adult: ?bool,
     *                 gender: ?int,
     *                 id: ?int,
     *                 known_for_department: ?string,
     *                 name: ?string,
     *                 original_name: ?string,
     *                 popularity: ?float,
     *                 profile_path: ?string,
     *                 credit_id: ?string,
     *                 department: ?string,
     *                 job: ?string,
     *             },
     *         >,
     *     },
     *     videos: ?array{
     *         id: ?int,
     *         results: ?array<
     *             int<0, max>,
     *             ?array{
     *                 iso_639_1: ?string,
     *                 iso_3166_1: ?string,
     *                 name: ?string,
     *                 key: ?string,
     *                 site: ?string,
     *                 size: ?string,
     *                 type: ?string,
     *                 official: ?bool,
     *                 published_at: ?string,
     *                 id: ?string,
     *             },
     *         >,
     *     },
     *     images: ?array{
     *         backdrops: ?array<
     *             int<0, max>,
     *             array{
     *                 aspect_ratio: ?float,
     *                 height: ?int,
     *                 iso_639_1: ?string,
     *                 file_path: ?string,
     *                 vote_average: ?float,
     *                 vote_count: ?int,
     *                 width: ?int,
     *             },
     *         >,
     *         id: ?int,
     *         logos: ?array<
     *              int<0, max>,
     *              array{
     *                  aspect_ratio: ?float,
     *                  height: ?int,
     *                  iso_639_1: ?string,
     *                  file_path: ?string,
     *                  vote_average: ?float,
     *                  vote_count: ?int,
     *                  width: ?int,
     *              },
     *          >,
     *         posters: ?array<
     *              int<0, max>,
     *              array{
     *                  aspect_ratio: ?float,
     *                  height: ?int,
     *                  iso_639_1: ?string,
     *                  file_path: ?string,
     *                  vote_average: ?float,
     *                  vote_count: ?int,
     *                  width: ?int,
     *              },
     *          >,
     *     },
     *     external_ids: ?array{
     *         id: ?int,
     *         imdb_id: ?string,
     *         wikidata_id: ?string,
     *         facebook_id: ?string,
     *         instagram_id: ?string,
     *         twitter_id: ?string,
     *     },
     *     keywords: ?array{
     *         id: ?int,
     *         keywords: ?array<
     *             int<0, max>,
     *             ?array{
     *                 id: ?int,
     *                 name: ?string,
     *             },
     *         >,
     *     },
     *     recommendations: ?array{
     *         page: ?int,
     *         results: ?array<
     *             int<0, max>,
     *             ?array{
     *                 adult: ?bool,
     *                 backdrop_path: ?string,
     *                 id: ?int,
     *                 title: ?string,
     *                 original_language: ?string,
     *                 original_name: ?string,
     *                 overview: ?string,
     *                 poster_path: ?string,
     *                 media_type: ?string,
     *                 genre_ids: ?array<int>,
     *                 popularity: ?float,
     *                 release_date: ?string,
     *                 vote_average: ?float,
     *                 vote_count: ?int,
     *                 origin_country: ?array<string>,
     *             }
     *         >,
     *         total_pages: ?int,
     *         total_results: ?int,
     *     },
     *     alternative_titles: ?array{
     *         id: ?int,
     *         results: ?array<
     *             int<0, max>,
     *             array{
     *                 iso_3166_1: ?string,
     *                 title: ?string,
     *                 type: ?string,
     *             },
     *         >,
     *     }
     *  }
     */
    public null|array $data;

    public TMDB $tmdb;

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function __construct(int $id)
    {
        // Adds extra logic for when a tmdb isn't found because it's a common
        // error that admins don't want to deal with. Hides 404s from logs via
        // App\Exceptions\Handler.php::dontReport, but still throws an exception
        // when the job is dispatched in sync for the FetchMeta.php command.
        $response = Http::acceptJson()
            ->retry(
                [1000, 5000, 15000],
                when: fn (Exception $exception) => !($exception instanceof RequestException && $exception->response->notFound()),
                throw: false
            )
            ->withUrlParameters(['id' => $id])
            ->get('https://api.TheMovieDB.org/3/movie/{id}', [
                'api_key'            => config('api-keys.tmdb'),
                'language'           => config('app.meta_locale'),
                'append_to_response' => 'videos,images,credits,external_ids,keywords,recommendations,alternative_titles',
            ])
            ->throwIf(fn (Response $response) => !$response->notFound());

        if ($response->notFound()) {
            throw new MetaFetchNotFoundException(
                $response->toException()->getMessage(),
                $response->toException()->getCode()
            );
        }

        $this->data = $response->json();

        $this->tmdb = new TMDB();
    }

    /**
     * @throws Exception
     * @return ?array{
     *     adult: bool,
     *     backdrop: ?string,
     *     budget: ?int,
     *     homepage: ?string,
     *     imdb_id: ?string,
     *     original_language: ?string,
     *     original_title: ?string,
     *     overview: ?string,
     *     popularity: ?float,
     *     poster: ?string,
     *     release_date: ?string,
     *     revenue: ?int,
     *     runtime: ?int,
     *     status: ?string,
     *     tagline: ?string,
     *     title: ?string,
     *     title_sort: ?string,
     *     vote_average: ?float,
     *     vote_count: ?int,
     * }
     */
    public function getMovie(): ?array
    {
        if ($this->data !== null && \array_key_exists('title', $this->data) && \is_string($this->data['title'])) {
            $titleSort = null;

            if ($this->data['release_date'] !== null) {
                $re = '/((?<nameSort>.*)(?<separator>\:|and)(?<remaining>.*)|(?<name>.*))/m';
                preg_match($re, $this->data['title'], $matches);

                $year = (new DateTime($this->data['release_date']))->format('Y');

                $titleSort = addslashes(str_replace(
                    ['The ', 'An ', 'A ', '"'],
                    [''],
                    Str::limit($matches['nameSort'] ?? $this->data['title'].' '.$year, 100)
                ));
            }

            return [
                'adult'             => $this->data['adult'] ?? false,
                'backdrop'          => $this->tmdb->image('backdrop', $this->data),
                'budget'            => $this->data['budget'] ?? null,
                'homepage'          => $this->data['homepage'] ?? null,
                'imdb_id'           => substr($this->data['imdb_id'] ?? '', 2),
                'original_language' => $this->data['original_language'] ?? null,
                'original_title'    => $this->data['original_title'] ?? null,
                'overview'          => $this->data['overview'] ?? null,
                'popularity'        => $this->data['popularity'] ?? null,
                'poster'            => $this->tmdb->image('poster', $this->data),
                'release_date'      => $this->tmdb->ifExists('release_date', $this->data),
                'revenue'           => $this->data['revenue'] ?? null,
                'runtime'           => $this->data['runtime'] ?? null,
                'status'            => $this->data['status'] ?? null,
                'tagline'           => $this->data['tagline'] ?? null,
                'title'             => Str::limit($this->data['title'], 200),
                'title_sort'        => $titleSort,
                'vote_average'      => $this->data['vote_average'] ?? null,
                'vote_count'        => $this->data['vote_count'] ?? null,
                'trailer'           => $this->data['videos']['results'][0]['key'] ?? null,
            ];
        }

        return null;
    }

    /**
     * @return array<int, array{
     *     id: ?int,
     *     name: ?string,
     * }>
     */
    public function getGenres(): array
    {
        $genres = [];

        foreach ($this->data['genres'] ?? [] as $genre) {
            $genres[] = [
                'id'   => $genre['id'] ?? null,
                'name' => $genre['name'] ?? null,
            ];
        }

        return $genres;
    }

    /**
     * @return array<
     *     int<0, max>,
     *     array{
     *         tmdb_movie_id: ?int,
     *         tmdb_person_id: ?int,
     *         occupation_id: value-of<Occupation>,
     *         character: ?string,
     *         order: ?int,
     *     },
     * >
     */
    public function getCredits(): array
    {
        $credits = [];

        foreach ($this->data['credits']['cast'] ?? [] as $person) {
            $credits[] = [
                'tmdb_movie_id'  => $this->data['id'] ?? null,
                'tmdb_person_id' => $person['id'] ?? null,
                'occupation_id'  => Occupation::ACTOR->value,
                'character'      => Str::limit($person['character'] ?? '', 200),
                'order'          => $person['order'] ?? null
            ];
        }

        foreach ($this->data['credits']['crew'] ?? [] as $person) {
            if (!\array_key_exists('job', $person) || $person['job'] === null) {
                continue;
            }

            $job = Occupation::from_tmdb_job($person['job']);

            if ($job !== null) {
                $credits[] = [
                    'tmdb_movie_id'  => $this->data['id'] ?? null,
                    'tmdb_person_id' => $person['id'] ?? null,
                    'occupation_id'  => $job->value,
                    'character'      => null,
                    'order'          => null
                ];
            }
        }

        return $credits;
    }

    /**
     * @return list<array{
     *     tmdb_movie_id: ?int,
     *     recommended_tmdb_movie_id: ?int,
     * }>
     */
    public function getRecommendations(): array
    {
        $movie_ids = \App\Models\TmdbMovie::query()
            ->select('id')
            ->whereIntegerInRaw('id', array_column($this->data['recommendations']['results'] ?? [], 'id'))
            ->pluck('id');

        $recommendations = [];

        foreach ($this->data['recommendations']['results'] ?? [] as $recommendation) {
            if ($recommendation === null || $recommendation['id'] === null || $this->data['id'] === null) {
                continue;
            }

            if ($movie_ids->contains($recommendation['id'])) {
                $recommendations[] = [
                    'tmdb_movie_id'             => $this->data['id'],
                    'recommended_tmdb_movie_id' => $recommendation['id'],
                ];
            }
        }

        return $recommendations;
    }
}
