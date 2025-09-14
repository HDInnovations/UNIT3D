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

use App\Models\Resurrection;
use App\Models\User;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserResurrections extends Component
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
    public string $rewarded = 'any';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function mount(int $userId): void
    {
        $this->user = User::find($userId);
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Resurrection>
     */
    final protected \Illuminate\Contracts\Pagination\LengthAwarePaginator $resurrections {
        get => Resurrection::query()
            ->select([
                'resurrections.id',
                'resurrections.created_at',
                'resurrections.seedtime',
                'resurrections.rewarded',
                'resurrections.torrent_id',
                DB::raw('history.active AND history.seeder AS seeding'),
                DB::raw('history.active AND NOT history.seeder AS leeching'),
                DB::raw('NOT history.active AND history.seeder AS completed'),
            ])
            ->with(['torrent', 'user'])
            ->leftJoin('torrents', 'torrents.id', '=', 'resurrections.torrent_id')
            ->leftJoin(
                'history',
                fn ($join) => $join
                    ->on('history.torrent_id', '=', 'resurrections.torrent_id')
                    ->on('history.user_id', '=', 'resurrections.user_id')
            )
            ->where('resurrections.user_id', '=', $this->user->id)
            ->when($this->rewarded === 'include', fn ($query) => $query->where('rewarded', '=', 1))
            ->when($this->rewarded === 'exclude', fn ($query) => $query->where('rewarded', '=', 0))
            ->when($this->name, fn ($query) => $query->where('name', 'like', '%'.str_replace(' ', '%', $this->name).'%'))
            ->when(
                \in_array($this->sortField, ['created_at', 'seedtime', 'rewarded']),
                fn ($query) => $query->orderBy('resurrections.'.$this->sortField, $this->sortDirection),
                fn ($query) => $query->orderBy('torrents.'.$this->sortField, $this->sortDirection)
            )
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-resurrections', [
            'resurrections' => $this->resurrections,
        ]);
    }
}
