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

namespace App\Jobs;

use App\Models\TmdbCompany;
use App\Models\TmdbCredit;
use App\Models\TmdbGenre;
use App\Models\TmdbNetwork;
use App\Models\TmdbPerson;
use App\Models\Torrent;
use App\Models\TmdbTv;
use App\Services\Tmdb\Client;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessTvJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * ProcessTvJob Constructor.
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
        return [
            Skip::when(cache()->has("tmdb-tv-scraper:{$this->id}")),
            new WithoutOverlapping((string) $this->id)->dontRelease()->expireAfter(30),
            new RateLimited('tmdb'),
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addDay();
    }

    public function handle(): void
    {
        // TMDB caches their api responses for 8 hours, so don't abuse them

        cache()->put("tmdb-tv-scraper:{$this->id}", now(), 8 * 3600);

        // Tv

        $tvScraper = new Client\TV($this->id);

        if ($tvScraper->getTv() === null) {
            return;
        }

        $tv = TmdbTv::updateOrCreate(['id' => $this->id], $tvScraper->getTv());

        // Companies

        $companies = [];

        foreach ($tvScraper->data['production_companies'] ?? [] as $company) {
            $companies[] = (new Client\Company($company['id']))->getCompany();
        }

        TmdbCompany::upsert($companies, 'id');
        $tv->companies()->sync(array_unique(array_column($companies, 'id')));

        // Networks

        $networks = [];

        foreach ($tvScraper->data['networks'] ?? [] as $network) {
            $networks[] = (new Client\Network($network['id']))->getNetwork();
        }

        TmdbNetwork::upsert($networks, 'id');
        $tv->networks()->sync(array_unique(array_column($networks, 'id')));

        // Genres

        TmdbGenre::upsert($tvScraper->getGenres(), 'id');
        $tv->genres()->sync(array_unique(array_column($tvScraper->getGenres(), 'id')));

        // People

        $credits = $tvScraper->getCredits();
        $people = [];

        foreach (array_unique(array_column($credits, 'tmdb_person_id')) as $personId) {
            // TMDB caches their api responses for 8 hours, so don't abuse them

            $cacheKey = "tmdb-person-scraper:{$personId}";

            if (cache()->has($cacheKey)) {
                continue;
            }

            cache()->put($cacheKey, now(), 8 * 3600);

            $people[] = (new Client\Person($personId))->getPerson();
        }

        TmdbPerson::upsert($people, 'id');
        TmdbCredit::where('tmdb_tv_id', '=', $this->id)->delete();
        TmdbCredit::upsert($credits, ['tmdb_person_id', 'tmdb_movie_id', 'tmdb_tv_id', 'occupation_id', 'character']);

        // Recommendations

        $tv->recommendedTv()->sync(array_unique(array_column($tvScraper->getRecommendations(), 'recommended_tmdb_tv_id')));

        Torrent::query()
            ->where('tmdb_tv_id', '=', $this->id)
            ->whereRelation('category', 'tv_meta', '=', true)
            ->searchable();
    }
}
