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

namespace App\Http\Livewire;

use App\Models\TmdbTv;
use App\Models\TmdbMovie;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class RandomMedia extends Component
{
    /**
     * Pick random IDs from appropriate Redis set (adult aware) with fallback.
     *
     * @param  string         $baseKey Base key suffix (e.g. 'movie' or 'tv')
     * @param  int            $count   Number of random members to retrieve
     * @return array<int,int>
     */
    private function pickIds(string $baseKey, int $count = 3): array
    {
        $prefix = config('cache.prefix');
        $useAdult = auth()->user()->settings?->show_adult_content !== false;
        $setKey = $useAdult ? "{$prefix}:random-media-{$baseKey}-ids" : "{$prefix}:random-media-{$baseKey}-ids-non-adult";

        $ids = Redis::connection('cache')->command('SRANDMEMBER', [$setKey, $count]) ?: [];

        if (!$useAdult && empty($ids)) {
            $ids = Redis::connection('cache')->command('SRANDMEMBER', ["{$prefix}:random-media-{$baseKey}-ids", $count]) ?: [];
        }

        return array_filter($ids); // Remove null/false values if any
    }

    /**
     * @var \Illuminate\Support\Collection<int, TmdbMovie>
     */
    final protected \Illuminate\Support\Collection $movies {
        get {
            $movieIds = $this->pickIds('movie');

            if (empty($movieIds)) {
                return collect();
            }

            return TmdbMovie::query()
                ->select(['id', 'backdrop', 'title', 'release_date'])
                ->withMin('torrents', 'category_id')
                ->whereIn('id', $movieIds)
                ->get();
        }
    }

    /**
     * @var \Illuminate\Support\Collection<int, TmdbTv>
     */
    final protected \Illuminate\Support\Collection $tvs {
        get {
            $tvIds = $this->pickIds('tv');

            if (empty($tvIds)) {
                return collect();
            }

            return TmdbTv::query()
                ->select(['id', 'backdrop', 'name', 'first_air_date'])
                ->withMin('torrents', 'category_id')
                ->whereIn('id', $tvIds)
                ->get();
        }
    }

    final public function render(): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.random-media', [
            'movies'  => $this->movies,
            'movies2' => $this->movies,
            'tvs'     => $this->tvs
        ]);
    }
}
