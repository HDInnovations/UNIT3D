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
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TV
{
    /**
     * @var ?array{
     *     adult: ?bool,
     *     backdrop_path: ?string,
     *     created_by: ?array<
     *         int<0, max>,
     *         array{
     *             id: ?int,
     *             credit_id: ?string,
     *             name: ?string,
     *             gender: ?int,
     *             profile_path: ?string,
     *         },
     *     >,
     *     episode_run_time: ?array<int>,
     *     first_air_date: ?string,
     *     genres: ?array<
     *         int<0, max>,
     *         array{
     *             id: ?int,
     *             name: ?string,
     *         },
     *     >,
     *     homepage: ?string,
     *     id: ?int,
     *     in_production: ?bool,
     *     languages: ?array<string>,
     *     last_air_date: ?string,
     *     last_episode_to_air: ?array{
     *         id: ?int,
     *         name: ?string,
     *         overview: ?string,
     *         vote_average: ?float,
     *         vote_count: ?int,
     *         air_date: ?string,
     *         episode_number: ?int,
     *         production_code: ?string,
     *         runtime: ?int,
     *         season_number: ?int,
     *         show_id: ?int,
     *         still_path: ?string,
     *     },
     *     name: ?string,
     *     next_episode_to_air: ?string,
     *     networks: ?array<
     *         int<0, max>,
     *         array{
     *             id: ?int,
     *             logo_path: ?string,
     *             name: ?string,
     *             origin_country: ?string,
     *         },
     *     >,
     *     number_of_episodes: ?int,
     *     number_of_seasons: ?int,
     *     origin_country: ?array<string>,
     *     original_language: ?string,
     *     original_name: ?string,
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
     *     seasons: ?array<
     *         int<0, max>,
     *         array{
     *             air_date: ?string,
     *             episode_count: ?int,
     *             id: ?int,
     *             name: ?string,
     *             overview: ?string,
     *             poster_path: ?string,
     *             season_number: ?int,
     *             vote_average: ?int,
     *         }
     *     >,
     *      spoken_languages: ?array<
     *          int<0, max>,
     *          array{
     *              english_name: ?string,
     *              iso_639_1: ?string,
     *              name: ?string,
     *          },
     *      >,
     *     status: ?string,
     *     tagline: ?string,
     *     type: ?string,
     *     vote_average: ?float,
     *     vote_count: ?int,
     *     aggregate_credits: ?array{
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
     *                 roles: ?array<
     *                     int<0, max>,
     *                     array{
     *                         credit_id: ?string,
     *                         character: ?string,
     *                         episode_count: ?int,
     *                     },
     *                 >,
     *                 total_episode_count: ?int,
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
     *                 jobs: ?array<
     *                     int<0, max>,
     *                     array{
     *                         credit_id: ?string,
     *                         job: ?string,
     *                         episode_count: ?int,
     *                     },
     *                 >,
     *                 department: ?string,
     *                 total_episode_count: ?int,
     *             },
     *         >,
     *         id: ?int,
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
     *         freebase_mid: ?string,
     *         freebase_id: ?string,
     *         tvdb_id: ?int,
     *         tvrage_id: ?int,
     *         wikidata_id: ?string,
     *         facebook_id: ?string,
     *         instagram_id: ?string,
     *         twitter_id: ?string,
     *     },
     *     keywords: ?array{
     *         id: ?int,
     *         results: ?array<
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
     *             int,
     *             ?array{
     *                 adult: ?bool,
     *                 backdrop_path: ?string,
     *                 id: ?int,
     *                 name: ?string,
     *                 original_language: ?string,
     *                 original_name: ?string,
     *                 overview: ?string,
     *                 poster_path: ?string,
     *                 media_type: ?string,
     *                 genre_ids: ?array<int>,
     *                 popularity: ?float,
     *                 first_air_date: ?string,
     *                 vote_average: ?float,
     *                 vote_count: ?int,
     *                 origin_country: ?array<string>,
     *             },
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
     * }
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
            ->get('https://api.TheMovieDB.org/3/tv/{id}', [
                'api_key'            => config('api-keys.tmdb'),
                'language'           => config('app.meta_locale'),
                'append_to_response' => 'videos,images,aggregate_credits,external_ids,keywords,recommendations,alternative_titles',
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
     * @return ?array{
     *     adult: bool,
     *     backdrop: ?string,
     *     episode_run_time: mixed,
     *     first_air_date: mixed,
     *     homepage: ?string,
     *     imdb_id: string,
     *     tvdb_id: string,
     *     in_production: ?bool,
     *     last_air_date: ?string,
     *     name: string,
     *     name_sort: string,
     *     number_of_episodes: ?int,
     *     number_of_seasons: ?int,
     *     origin_country: mixed,
     *     original_language: ?string,
     *     original_name: ?string,
     *     overview: ?string,
     *     popularity: ?float,
     *     poster: ?string,
     *     status: ?string,
     *     vote_average: ?float,
     *     vote_count: ?int,
     * }
     */
    public function getTv(): ?array
    {
        if (isset($this->data['id'], $this->data['name'])) {
            return [
                'adult'              => $this->data['adult'] ?? false,
                'backdrop'           => $this->tmdb->image('backdrop', $this->data),
                'episode_run_time'   => $this->tmdb->ifHasItems('episode_run_time', $this->data),
                'first_air_date'     => $this->tmdb->ifExists('first_air_date', $this->data),
                'homepage'           => $this->data['homepage'] ?? null,
                'imdb_id'            => substr($this->data['external_ids']['imdb_id'] ?? '', 2),
                'tvdb_id'            => (string) $this->data['external_ids']['tvdb_id'],
                'in_production'      => $this->data['in_production'] ?? null,
                'last_air_date'      => $this->data['last_air_date'] ?? null,
                'name'               => Str::limit($this->data['name'], 200),
                'name_sort'          => addslashes(str_replace(['The ', 'An ', 'A ', '"'], [''], Str::limit($this->data['name'], 100))),
                'number_of_episodes' => $this->data['number_of_episodes'] ?? null,
                'number_of_seasons'  => $this->data['number_of_seasons'] ?? null,
                'origin_country'     => $this->tmdb->ifHasItems('origin_country', $this->data),
                'original_language'  => $this->data['original_language'] ?? null,
                'original_name'      => $this->data['original_name'] ?? null,
                'overview'           => $this->data['overview'] ?? null,
                'popularity'         => $this->data['popularity'] ?? null,
                'poster'             => $this->tmdb->image('poster', $this->data),
                'status'             => $this->data['status'] ?? null,
                'vote_average'       => $this->data['vote_average'] ?? null,
                'vote_count'         => $this->data['vote_count'] ?? null,
                'trailer'            => $this->data['videos']['results'][0]['key'] ?? null,
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
                'id'   => $genre['id'],
                'name' => $genre['name'],
            ];
        }

        return $genres;
    }

    /**
     * @return array<
     *     int<0, max>,
     *     array{
     *         tmdb_tv_id: ?int,
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

        foreach ($this->data['aggregate_credits']['cast'] ?? [] as $person) {
            foreach ($person['roles'] ?? [] as $role) {
                $credits[] = [
                    'tmdb_tv_id'     => $this->data['id'],
                    'tmdb_person_id' => $person['id'],
                    'occupation_id'  => Occupation::ACTOR->value,
                    'character'      => Str::limit($role['character'] ?? '', 200),
                    'order'          => $person['order'] ?? null
                ];
            }
        }

        foreach ($this->data['aggregate_credits']['crew'] ?? [] as $person) {
            foreach ($person['jobs'] ?? [] as $job) {
                if (!\array_key_exists('job', $job) || $job['job'] === null) {
                    continue;
                }

                $occupation = Occupation::from_tmdb_job($job['job']);

                if ($occupation !== null) {
                    $credits[] = [
                        'tmdb_tv_id'     => $this->data['id'],
                        'tmdb_person_id' => $person['id'],
                        'occupation_id'  => $occupation->value,
                        'character'      => null,
                        'order'          => null,
                    ];
                }
            }
        }

        foreach ($this->data['created_by'] ?? [] as $person) {
            $credits[] = [
                'tmdb_tv_id'     => $this->data['id'],
                'tmdb_person_id' => $person['id'],
                'occupation_id'  => Occupation::CREATOR->value,
                'character'      => null,
                'order'          => null,
            ];
        }

        return $credits;
    }

    /**
     * @return list<array{
     *     tmdb_tv_id: ?int,
     *     recommended_tmdb_tv_id: ?int,
     * }>
     */
    public function getRecommendations(): array
    {
        $tv_ids = \App\Models\TmdbTv::query()
            ->select('id')
            ->whereIntegerInRaw('id', array_column($this->data['recommendations']['results'] ?? [], 'id'))
            ->pluck('id');

        $recommendations = [];

        foreach ($this->data['recommendations']['results'] ?? [] as $recommendation) {
            if ($recommendation === null || $recommendation['id'] === null || $this->data['id'] === null) {
                continue;
            }

            if ($tv_ids->contains($recommendation['id'])) {
                $recommendations[] = [
                    'tmdb_tv_id'             => $this->data['id'],
                    'recommended_tmdb_tv_id' => $recommendation['id'],
                ];
            }
        }

        return $recommendations;
    }
}
