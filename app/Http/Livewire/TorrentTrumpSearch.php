<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.tx
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\Models\TorrentTrump;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TorrentTrumpSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $torrentName = '';

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $sortField = 'id';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 25;

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, TorrentTrump>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $torrentTrumps {
        get => TorrentTrump::query()
            ->with([
                'user:id,username,group_id,deleted_at',
                'torrent:id,name,deleted_at',
            ])
            ->when($this->torrentName !== '', fn ($query) => $query->whereRelation('torrent', 'name', 'LIKE', '%'.$this->torrentName.'%'))
            ->when($this->username !== '', fn ($query) => $query->whereRelation('user', 'username', '=', $this->username))
            ->when($this->sortField !== '', fn ($query) => $query->orderBy($this->sortField, $this->sortDirection))
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-trump-search', [
            'torrentTrumps' => $this->torrentTrumps,
        ])
            ->extends('layout.default')
            ->section('content');
    }
}
