<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Services\Data;

final class Genre
{
    public $genres;

    /**
     * @var string[]
     */
    protected array $movieGenres = [
        'Action',
        'Adventure',
        'Animation',
        'Biography',
        'Comedy',
        'Crime',
        'Documentary',
        'Drama',
        'Family',
        'Fantasy',
        'History',
        'Horror',
        'Music',
        'Musical',
        'Mystery',
        'Romance',
        'Science Fiction',
        'Sport',
        'Thriller',
        'War',
        'Western',
    ];

    /**
     * @var string[]
     */
    protected array $tvGenres = [
        'Game-Show',
        'News',
        'Reality-TV',
        'Sitcom',
        'Talk-Show',
        'Thriller',
    ];

    public function __construct(array $genres)
    {
        $this->genres = $this->parseGenres($genres);
    }

    /**
     * @param $genres
     *
     * @return mixed[]
     */
    private function parseGenres($genres): array
    {
        $myGenre = [];
        $genreCollection = $this->movieGenres + $this->tvGenres;
        foreach ($genres as $genre) {
            if (in_array($genre, $genreCollection)) {
                $myGenre[] = $genre;
            } elseif ($matchedGenre = $this->matchGenre($genre)) {
                $myGenre[] = $matchedGenre;
            }
        }

        return $myGenre;
    }

    /**
     * @param $genre
     * @return string|bool
     */
    private function matchGenre($genre)
    {
        if ($genre == 'Sci-Fi') {
            return 'Science Fiction';
        }

        return false;
    }
}
