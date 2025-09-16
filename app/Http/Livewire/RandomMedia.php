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
     * @var \Illuminate\Support\Collection<int, TmdbMovie>
     */
    final protected \Illuminate\Support\Collection $movies {
        get {
            $cacheKey = config('cache.prefix').':random-media-movie-ids';

            $movieIds = Redis::connection('cache')->command('SRANDMEMBER', [$cacheKey, 3]);

            return TmdbMovie::query()
                ->select(['id', 'backdrop', 'title', 'release_date'])
                ->withMin('torrents', 'category_id')
                ->whereIn('id', $movieIds)
                ->when(
                    auth()->user()->settings?->hide_adult_content,
                    fn ($query) => $query->where('adult', '=', false)
                )
                ->get();
        }
    }

    /**
     * @var \Illuminate\Support\Collection<int, TmdbTv>
     */
    final protected \Illuminate\Support\Collection $tvs {
        get {
            $cacheKey = config('cache.prefix').':random-media-tv-ids';

            $tvIds = Redis::connection('cache')->command('SRANDMEMBER', [$cacheKey, 3]);

            return TmdbTv::query()
                ->select(['id', 'backdrop', 'name', 'first_air_date'])
                ->withMin('torrents', 'category_id')
                ->whereIn('id', $tvIds)
                ->when(
                    auth()->user()->settings?->hide_adult_content,
                    fn ($query) => $query->where('adult', '=', false)
                )
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
