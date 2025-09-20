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

namespace App\Console\Commands;

use App\Models\Torrent;
use App\Jobs\ProcessIgdbGameJob;
use App\Jobs\ProcessMovieJob;
use App\Jobs\ProcessTvJob;
use App\Services\Igdb\IgdbScraper;
use App\Services\Tmdb\TMDBScraper;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class FetchMeta extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'fetch:meta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches meta data for new system on preexisting torrents';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $start = now();
        $this->alert('Meta fetch queueing started. Fetching is done synchronously within this command. This can take awhile (~1 work per second).');

        $tmdbScraper = new TMDBScraper();
        $igdbScraper = new IgdbScraper();

        $this->info('Querying all tmdb movie ids');

        $tmdbMovieIds = Torrent::query()
            ->whereRelation('category', 'movie_meta', '=', true)
            ->select('tmdb_movie_id')
            ->distinct()
            ->whereNotNull('tmdb_movie_id')
            ->pluck('tmdb_movie_id');

        $this->info('Fetching '.$tmdbMovieIds->count().' movies');

        foreach ($tmdbMovieIds as $id) {
            usleep(250_000);

            try {
                ProcessMovieJob::dispatchSync($id);
                $this->info("Movie metadata fetched for tmdb {$id}");
            } catch (Exception $e) {
                $this->warn("Movie metadata fetch failed for tmdb {$id}: ".$e->getMessage());
            }
        }

        $this->info('Querying all tmdb tv ids');

        $tmdbTvIds = Torrent::query()
            ->whereRelation('category', 'tv_meta', '=', true)
            ->select('tmdb_tv_id')
            ->distinct()
            ->whereNotNull('tmdb_tv_id')
            ->pluck('tmdb_tv_id');

        $this->info('Fetching '.$tmdbTvIds->count().' tv series');

        foreach ($tmdbTvIds as $id) {
            usleep(250_000);

            try {
                ProcessTvJob::dispatchSync($id);
                $this->info("TV metadata fetched for tmdb {$id}");
            } catch (Exception $e) {
                $this->warn("TV metadata fetch failed for tmdb {$id}: ".$e->getMessage());
            }
        }

        $this->info('Querying all igdb game ids');

        $igdbGameIds = Torrent::query()
            ->whereRelation('category', 'game_meta', '=', true)
            ->select('igdb')
            ->distinct()
            ->whereNotNull('igdb')
            ->pluck('igdb');

        $this->info('Fetching '.$igdbGameIds->count().' games');

        foreach ($igdbGameIds as $id) {
            usleep(250_000);

            try {
                ProcessIgdbGameJob::dispatchSync($id);
                $this->info("Game metadata fetched for igdb {$id}");
            } catch (Exception $e) {
                $this->warn("Game metadata fetch failed for igdb {$id}: ".$e->getMessage());
            }
        }

        $this->alert('Meta fetch queueing complete in '.(int) now()->diffInSeconds($start, true).'s.');
    }
}
