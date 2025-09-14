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

use App\Models\History;
use App\Models\Peer;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: true)]
class PeerStats extends Component
{
    final protected int $leecherCount {
        get => (int) cache()->flexible(
            'peer-stats:leecher-count',
            [10 * 60, 30 * 60],
            // Generally sites have more seeders than leechers, so it ends up being faster (by approximately 50%) to compute leechers and total instead of seeders and leechers.
            fn () => Peer::query()->where('seeder', '=', false)->where('active', '=', true)->count()
        );
    }

    final protected int $peerCount {
        get => (int) cache()->flexible(
            'peer-stats:peer-count',
            [10 * 60, 30 * 60],
            fn () => Peer::query()->where('active', '=', true)->count(),
        );
    }

    final protected int $totalSeeded {
        get => (int) cache()->flexible(
            'peer-stats:total-seeded',
            [10 * 60, 30 * 60],
            fn () => History::query()
                ->join('torrents', 'history.torrent_id', '=', 'torrents.id')
                ->where('history.active', '=', true)
                ->where('history.seeder', '=', true)
                ->sum('size'),
        );
    }

    final public function placeholder(): string
    {
        return <<<'HTML'
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.peers') }}</h2>
            <div class="panel__body">Loading...</div>
        </section>
        HTML;
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.stats.peer-stats', [
            'num_seeders'  => $this->peerCount - $this->leecherCount,
            'num_leechers' => $this->leecherCount,
            'num_peers'    => $this->peerCount,
            'totalSeeded'  => $this->totalSeeded,
        ]);
    }
}
