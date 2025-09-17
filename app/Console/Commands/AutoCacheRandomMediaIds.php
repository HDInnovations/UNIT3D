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

namespace App\Console\Commands;

use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\Redis;
use Throwable;

class AutoCacheRandomMediaIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:cache_random_media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Caches valid media ids for random media component';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $prefix = config('cache.prefix');

        [$movieIds, $nonAdultMovieIds] = $this->collectIds(TmdbMovie::query());
        [$tvIds, $nonAdultTvIds] = $this->collectIds(TmdbTv::query());

        $this->storeSet($prefix.':random-media-movie-ids', $movieIds);
        $this->storeSet($prefix.':random-media-movie-ids-non-adult', $nonAdultMovieIds);
        $this->storeSet($prefix.':random-media-tv-ids', $tvIds);
        $this->storeSet($prefix.':random-media-tv-ids-non-adult', $nonAdultTvIds);

        $this->comment(
            \sprintf(
                'Cached %d movies (%d non-adult) and %d tv (%d non-adult).',
                $movieIds->count(),
                $nonAdultMovieIds->count(),
                $tvIds->count(),
                $nonAdultTvIds->count()
            )
        );
    }

    /**
     * Collect full and non-adult IDs for a given base query (movie or tv).
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     * @param  \Illuminate\Database\Eloquent\Builder<T>                                                $base
     * @return array{\Illuminate\Support\Collection<int,int>, \Illuminate\Support\Collection<int,int>}
     */
    private function collectIds($base): array
    {
        $base = $base
            ->select('id')
            ->whereHas('torrents')
            ->whereNotNull('backdrop');

        $all = (clone $base)->pluck('id');
        $nonAdult = (clone $base)
            ->where(function ($q): void {
                $q->where('adult', '=', false)->orWhereNull('adult');
            })
            ->pluck('id');

        return [$all, $nonAdult];
    }

    /**
     * Store a Redis set (clearing previous contents). Skip empty collections.
     *
     * @param string                                  $key
     * @param \Illuminate\Support\Collection<int,int> $ids
     */
    private function storeSet(string $key, $ids): void
    {
        if ($ids->isEmpty()) {
            return;
        }

        $redis = Redis::connection('cache');
        $redis->command('DEL', [$key]);
        $redis->command('SADD', [$key, ...$ids]);
    }
}
