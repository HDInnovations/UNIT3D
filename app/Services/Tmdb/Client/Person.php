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

use App\Services\Tmdb\TMDB;
use Illuminate\Support\Facades\Http;

class Person
{
    /**
     * @var array{
     *     adult: ?bool,
     *     also_known_as: ?array<string>,
     *     biography: ?string,
     *     birthday: ?string,
     *     deathday: ?string,
     *     gender: ?int,
     *     homepage: ?string,
     *     id: ?int,
     *     imdb_id: ?string,
     *     known_for_department: ?string,
     *     name: ?string,
     *     place_of_birth: ?string,
     *     popularity: ?float,
     *     profile_path: ?string,
     * }
     */
    public array $data;

    public TMDB $tmdb;

    public function __construct(int $id)
    {
        $this->data = Http::acceptJson()
            ->retry([1000, 5000, 15000])
            ->withUrlParameters(['id' => $id])
            ->get('https://api.TheMovieDB.org/3/person/{id}', [
                'api_key'            => config('api-keys.tmdb'),
                'language'           => config('app.meta_locale'),
                'append_to_response' => 'images,credits',
            ])
            ->json();

        $this->tmdb = new TMDB();
    }

    /**
     * @return array{
     *     id: ?int,
     *     birthday: ?string,
     *     known_for_department: ?string,
     *     deathday: ?string,
     *     name: ?string,
     *     gender: ?int,
     *     biography: ?string,
     *     popularity: ?float,
     *     place_of_birth: ?string,
     *     still: ?string,
     *     adult: ?bool,
     *     imdb_id: ?string,
     *     homepage: ?string,
     * }
     */
    public function getPerson(): array
    {
        return [
            'id'                   => $this->data['id'] ?? null,
            'birthday'             => $this->data['birthday'] ?? null,
            'known_for_department' => $this->data['known_for_department'] ?? null,
            'deathday'             => $this->data['deathday'] ?? null,
            'name'                 => $this->data['name'] ?? null,
            'gender'               => $this->data['gender'] ?? null,
            'biography'            => $this->data['biography'] ?? null,
            'popularity'           => $this->data['popularity'] ?? null,
            'place_of_birth'       => $this->data['place_of_birth'] ?? null,
            'still'                => $this->tmdb->image('profile', $this->data),
            'adult'                => $this->data['adult'] ?? null,
            'imdb_id'              => $this->data['imdb_id'] ?? null,
            'homepage'             => $this->data['homepage'] === null || \strlen($this->data['homepage']) > 255 ? null : $this->data['homepage'],
        ];
    }
}
