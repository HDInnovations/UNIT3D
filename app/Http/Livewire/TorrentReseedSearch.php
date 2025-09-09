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

namespace App\Http\Livewire;

use App\Models\Torrent;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TorrentReseedSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $torrentName = '';

    #[Url(history: true)]
    public bool $myRequests = false;

    #[Url(history: true)]
    public string $sortField = 'reseeds_min_created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function updatingTorrentName(): void
    {
        $this->resetPage();
    }

    final public function updatingMyRequests(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Torrent>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $torrents {
        get => Torrent::query()
            ->whereHas(
                'reseeds',
                fn ($query) => $query
                    ->when($this->myRequests, fn ($query) => $query->where('user_id', '=', auth()->id()))
            )
            ->with('reseeds.user.group')
            ->withCount('reseeds')
            ->withMin('reseeds', 'created_at')
            ->withCasts([
                'reseeds_min_created_at' => 'datetime',
            ])
            ->when($this->torrentName !== '', fn ($query) => $query->where('name', 'LIKE', '%'.$this->torrentName.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-reseed-search', [
            'torrents' => $this->torrents,
        ]);
    }
}
