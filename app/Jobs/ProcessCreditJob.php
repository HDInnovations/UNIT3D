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

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\TmdbCredit;
use App\Models\TmdbPerson;
use App\Services\Tmdb\Client;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class ProcessCreditJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param list<array{
     *     tmdb_movie_id: ?int,
     *     tmdb_person_id: ?int,
     *     occupation_id: value-of<\App\Enums\Occupation>,
     *     character: ?string,
     *     order: ?int,
     * }>|list<array{
     *     tmdb_tv_id: ?int,
     *     tmdb_person_id: ?int,
     *     occupation_id: value-of<\App\Enums\Occupation>,
     *     character: ?string,
     *     order: ?int,
     * }> $credits
     * @return void
     */
    public function __construct(public array $credits)
    {
    }

    /**
     * The number of seconds the job can run before timing out.
     *
     * Some shows have 2000+ credits requiring more than the default of 60 seconds.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            new RateLimited(GlobalRateLimit::TMDB),
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
        $people = [];
        $cache = [];

        foreach (array_unique(array_column($this->credits, 'tmdb_person_id')) as $personId) {
            // TMDB caches their api responses for 8 hours, so don't abuse them

            $cacheKey = "tmdb-person-scraper:{$personId}";

            if (cache()->has($cacheKey)) {
                continue;
            }

            $people[] = (new Client\Person($personId))->getPerson();

            $cache[$cacheKey] = now();
        }

        foreach (collect($people)->chunk(intdiv(65_000, 13)) as $people) {
            TmdbPerson::query()->upsert($people->toArray(), 'id');
        }

        if ($cache !== []) {
            cache()->put($cache, 8 * 3600);
        }

        TmdbCredit::query()->upsert($this->credits, ['tmdb_person_id', 'tmdb_movie_id', 'tmdb_tv_id', 'occupation_id', 'character']);
    }
}
