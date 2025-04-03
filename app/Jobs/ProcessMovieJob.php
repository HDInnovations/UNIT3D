<?php

declare(strict_types=1);

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

namespace App\Jobs;

use App\Models\TmdbCollection;
use App\Models\TmdbCompany;
use App\Models\TmdbCredit;
use App\Models\TmdbGenre;
use App\Models\TmdbMovie;
use App\Models\TmdbPerson;
use App\Models\TmdbRecommendation;
use App\Models\Torrent;
use App\Services\Tmdb\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessMovieJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ProcessMovieJob constructor.
     */
    public function __construct(public int $id)
    {
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new WithoutOverlapping((string) $this->id)->dontRelease()->expireAfter(30)];
    }

    public function handle(): void
    {
        // Movie

        $movieScraper = new Client\Movie($this->id);

        if ($movieScraper->getMovie() === null) {
            return;
        }

        $movie = TmdbMovie::updateOrCreate(['id' => $this->id], $movieScraper->getMovie());

        // Genres

        TmdbGenre::upsert($movieScraper->getGenres(), 'id');
        $movie->genres()->sync(array_unique(array_column($movieScraper->getGenres(), 'id')));

        // Companies

        $companies = [];

        foreach ($movieScraper->data['production_companies'] ?? [] as $company) {
            $companies[] = (new Client\Company($company['id']))->getCompany();
        }

        TmdbCompany::upsert($companies, 'id');
        $movie->companies()->sync(array_unique(array_column($companies, 'id')));

        // Collection

        if ($movieScraper->data['belongs_to_collection'] !== null) {
            $collection = (new Client\Collection($movieScraper->data['belongs_to_collection']['id']))->getCollection();

            TmdbCollection::upsert($collection, 'id');
            $movie->collections()->sync([$collection['id']]);
        }

        // People

        $credits = $movieScraper->getCredits();
        $people = [];

        foreach (array_unique(array_column($credits, 'tmdb_person_id')) as $person_id) {
            $people[] = (new Client\Person($person_id))->getPerson();
        }

        TmdbPerson::upsert($people, 'id');
        TmdbCredit::where('tmdb_movie_id', '=', $this->id)->delete();
        TmdbCredit::upsert($credits, ['tmdb_person_id', 'tmdb_movie_id', 'tmdb_tv_id', 'occupation_id', 'character']);

        // Recommendations

        TmdbRecommendation::upsert($movieScraper->getRecommendations(), ['recommended_tmdb_movie_id', 'tmdb_movie_id']);

        Torrent::query()
            ->where('tmdb_movie_id', '=', $this->id)
            ->whereRelation('category', 'movie_meta', '=', true)
            ->searchable();
    }
}
