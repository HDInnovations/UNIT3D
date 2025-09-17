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

use App\Models\Peer;
use App\Models\User;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserActive extends Component
{
    use LivewireSort;
    use WithPagination;

    public ?User $user = null;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $name = '';

    #[Url(history: true)]
    public string $ip = '';

    #[Url(history: true)]
    public string $port = '';

    #[Url(history: true)]
    public string $client = '';

    #[Url(history: true)]
    public string $seeding = 'any';

    #[Url(history: true)]
    public string $active = 'include';

    #[Url(history: true)]
    public string $visible = 'any';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public bool $showMorePrecision = false;

    final public function mount(int $userId): void
    {
        $this->user = User::find($userId);
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Peer>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $actives {
        get => Peer::query()
            ->join('torrents', 'peers.torrent_id', '=', 'torrents.id')
            ->select(
                'peers.port',
                'peers.agent',
                'peers.uploaded',
                'peers.downloaded',
                'peers.left',
                'peers.seeder',
                'peers.created_at',
                'peers.updated_at',
                'peers.torrent_id',
                'peers.user_id',
                'peers.active',
                'peers.visible',
                'peers.connectable',
                'torrents.name',
                'torrents.size',
                'torrents.seeders',
                'torrents.leechers',
                'torrents.times_completed',
                DB::raw('peers.active AND peers.seeder AS seeding'),
                DB::raw('peers.active AND NOT peers.seeder AS leeching'),
            )
            ->selectRaw('INET6_NTOA(ip) as ip')
            ->selectRaw('(1 - (peers.left / COALESCE(torrents.size, 0))) AS progress')
            ->where('peers.user_id', '=', $this->user->id)
            ->when(
                $this->name,
                fn ($query) => $query
                    ->where('name', 'like', '%'.str_replace(' ', '%', $this->name).'%')
            )
            ->when($this->ip !== '', fn ($query) => $query->having('ip', '=', $this->ip))
            ->when($this->port !== '', fn ($query) => $query->where('port', '=', $this->port))
            ->when($this->client !== '', fn ($query) => $query->where('agent', '=', $this->client))
            ->when($this->seeding === 'include', fn ($query) => $query->where('seeder', '=', 1))
            ->when($this->seeding === 'exclude', fn ($query) => $query->where('seeder', '=', 0))
            ->when($this->active === 'include', fn ($query) => $query->where('active', '=', 1))
            ->when($this->active === 'exclude', fn ($query) => $query->where('active', '=', 0))
            ->when($this->visible === 'include', fn ($query) => $query->where('visible', '=', 1))
            ->when($this->visible === 'exclude', fn ($query) => $query->where('visible', '=', 0))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-active', [
            'actives' => $this->actives,
        ]);
    }
}
