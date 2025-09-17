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

namespace App\Http\Livewire\Stats;

use App\Models\Category;
use App\Models\Resolution;
use App\Models\Torrent;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: true)]
class TorrentStats extends Component
{
    final protected int $totalCount {
        get => (int) cache()->flexible(
            'torrent-stats:total-count',
            [10 * 60, 30 * 60],
            fn () => Torrent::query()->count(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Resolution>
     */
    final protected \Illuminate\Database\Eloquent\Collection $resolutions {
        get => cache()->flexible(
            'torrent-stats:resolutions',
            [10 * 60, 30 * 60],
            fn () => Resolution::query()->withCount('torrents')->orderBy('position')->get(),
        );
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, Category>
     */
    final protected \Illuminate\Database\Eloquent\Collection $categories {
        get => cache()->flexible(
            'torrent-stats:categories',
            [10 * 60, 30 * 60],
            fn () => Category::query()->withCount('torrents')->orderBy('position')->get(),
        );
    }

    final protected int $sizeSum {
        get => (int) cache()->flexible(
            'torrent-stats:size-sum',
            [10 * 60, 30 * 60],
            fn () => Torrent::query()->sum('size'),
        );
    }

    final public function placeholder(): string
    {
        return <<<'HTML'
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
            <div class="panel__body">Loading...</div>
        </section>
        HTML;
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.stats.torrent-stats', [
            'num_torrent'  => $this->totalCount,
            'categories'   => $this->categories,
            'resolutions'  => $this->resolutions,
            'torrent_size' => $this->sizeSum,
        ]);
    }
}
