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

use App\Models\UnregisteredInfoHash;
use App\Models\User;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserUnregisteredInfoHashSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    public ?User $user = null;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'deleted_at';

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
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, UnregisteredInfoHash>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $unregisteredInfoHashes {
        get => UnregisteredInfoHash::query()
            ->select([
                'unregistered_info_hashes.info_hash',
                'unregistered_info_hashes.updated_at',
                'torrents.id',
                'torrents.name',
                'torrents.size',
                'torrents.deleted_at',
            ])
            ->withCasts([
                'deleted_at' => 'datetime',
            ])
            ->join('torrents', 'unregistered_info_hashes.info_hash', '=', 'torrents.info_hash')
            ->whereBelongsTo($this->user)
            ->whereNotNull('torrents.deleted_at')
            ->where('unregistered_info_hashes.updated_at', '>', now()->subHours(2))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-unregistered-info-hash-search', [
            'unregisteredInfoHashes' => $this->unregisteredInfoHashes,
        ]);
    }
}
