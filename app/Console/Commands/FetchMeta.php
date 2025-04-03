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
    protected $description = 'Fetches Meta Data For New System On Preexisting Torrents';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $start = now();
        $this->alert('Meta fetch queueing started. Fetching is done asynchronously in a separate job queue.');

        $tmdbScraper = new TMDBScraper();
        $igdbScraper = new IgdbScraper();

        $this->info('Querying all tmdb movie ids');

        $tmdbMovieIds = Torrent::query()
            ->whereRelation('category', 'movie_meta', '=', true)
            ->select('tmdb_movie_id')
            ->distinct()
            ->whereNotNull('tmdb_movie_id')
            ->pluck('tmdb_movie_id');

        $this->info('Queueing all tmdb movie metadata fetching');

        foreach ($tmdbMovieIds as $id) {
            sleep(3);
            $tmdbScraper->movie($id);
            $this->info("Movie metadata fetched for tmdb {$id}");
        }

        $this->info('Querying all tmdb tv ids');

        $tmdbTvIds = Torrent::query()
            ->whereRelation('category', 'tv_meta', '=', true)
            ->select('tmdb_tv_id')
            ->distinct()
            ->whereNotNull('tmdb_tv_id')
            ->pluck('tmdb_tv_id');

        $this->info('Queueing all tmdb tv metadata fetching');

        foreach ($tmdbTvIds as $id) {
            sleep(3);
            $tmdbScraper->tv($id);
            $this->info("Movie metadata fetched for tmdb {$id}");
        }

        $this->info('Querying all igdb game ids');

        $igdbGameIds = Torrent::query()
            ->whereRelation('category', 'game_meta', '=', true)
            ->select('igdb')
            ->distinct()
            ->whereNotNull('igdb')
            ->pluck('igdb');

        $this->info('Queueing all igdb game metadata fetching');

        foreach ($igdbGameIds as $id) {
            usleep(250_000);
            $igdbScraper->game($id);
            $this->info("Game metadata fetched for igdb {$id}");
        }

        $this->alert('Meta fetch queueing complete in '.now()->floatDiffInSeconds($start).'s.');
    }
}
