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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Services\Igdb\Client;

use App\Exceptions\MetaFetchNotFoundException;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class Game
{
    /**
     * @var null|array<array{
     *     id: int,
     *     name?: string,
     *     summary?: string,
     *     first_release_date?: int, // unix timestamp
     *     url?: string,
     *     rating?: float,
     *     rating_count?: int,
     *     cover?: array{
     *         id: int,
     *         image_id: string,
     *     },
     *     artworks?: list<array{
     *         id: int,
     *         image_id: string
     *     }>,
     *     genres?: list<array{
     *         id: int,
     *         name?: string,
     *     }>,
     *     videos?: list<array{
     *         id: int,
     *         video_id: string,
     *         name?: string,
     *     }>,
     *     involved_companies?: list<array{
     *         id: int,
     *         company: array{
     *             id: int,
     *             name?: string,
     *             url?: string,
     *             logo?: array{
     *                 id: int,
     *                 image_id: string,
     *             },
     *         },
     *     }>,
     *     platforms?: list<array{
     *             id: int,
     *             name?: string,
     *             platform_logo?: array{
     *                 id: int,
     *                 image_id: string,
     *             },
     *     }>,
     *  }>
     */
    public null|array $data;

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws MetaFetchNotFoundException
     */
    public function __construct(int $id)
    {
        $cacheKey = 'igdb-access-token';

        $accessToken = cache()->get($cacheKey);

        if (!$accessToken) {
            $response = Http::withQueryParameters([
                'client_id'     => config('igdb.credentials.client_id'),
                'client_secret' => config('igdb.credentials.client_secret'),
                'grant_type'    => 'client_credentials',
            ])
                ->post('https://id.twitch.tv/oauth2/token')
                ->throw()
                ->json();

            if (
                !\is_array($response)
                || !\array_key_exists('access_token', $response)
                || !\is_string($response['access_token'])
                || !\array_key_exists('expires_in', $response)
                || !\is_int($response['expires_in'])
            ) {
                throw new Exception('IGDB authentication error');
            }

            cache()->put($cacheKey, $response['access_token'], $response['expires_in'] - 60);

            $accessToken = $response['access_token'];
        }

        // Adds extra logic for when a igdb isn't found because it's a common
        // error that admins don't want to deal with. Hides 404s from logs via
        // App\Exceptions\Handler.php::dontReport, but still throws an exception
        // when the job is dispatched in sync for the FetchMeta.php command.
        $response = Http::acceptJson()
            ->withHeaders([
                'Client-ID' => config('igdb.credentials.client_id'),
            ])
            ->withToken($accessToken)
            ->retry(
                [1000, 5000, 15000],
                when: fn (Exception $exception) => !($exception instanceof RequestException && $exception->response->notFound()),
                throw: false
            )
            ->withBody(
                'fields '.implode(',', [
                    'id',
                    'name',
                    'summary',
                    'first_release_date',
                    'url',
                    'rating',
                    'rating_count',
                    'cover.image_id',
                    'artworks.image_id',
                    'genres.id',
                    'genres.name',
                    'videos.video_id',
                    'videos.name',
                    'involved_companies.company.id',
                    'involved_companies.company.name',
                    'involved_companies.company.url',
                    'involved_companies.company.logo.image_id',
                    'platforms.id',
                    'platforms.name',
                    'platforms.platform_logo.image_id',
                ]).'; where id = '.$id.';',
                'plain/text'
            )
            ->post('https://api.igdb.com/v4/games')
            ->throwIf(fn (Response $response) => !$response->notFound());

        if ($response->notFound()) {
            throw new MetaFetchNotFoundException(
                $response->toException()->getMessage(),
                $response->toException()->getCode()
            );
        }

        $this->data = $response->json();
    }
}
