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

use App\Models\Scopes\ApprovedScope;
use App\Models\UnregisteredInfoHash;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UnregisteredInfoHashSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public bool $excludeSoftDeletedTorrents = true;

    #[Url(history: true)]
    public string $groupBy = 'none';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, UnregisteredInfoHash>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $unregisteredInfoHashes {
        get => UnregisteredInfoHash::query()
            ->with('user.group')
            ->when($this->username !== '', fn ($query) => $query->whereRelation('user', 'username', 'LIKE', '%'.$this->username.'%'))
            ->when(
                $this->groupBy === 'info_hash',
                fn ($query) => $query->groupBy('info_hash')
                    ->select([
                        'info_hash',
                        DB::raw('MIN(created_at) as created_at'),
                        DB::raw('MAX(updated_at) as updated_at'),
                        DB::raw('COUNT(*) as amount'),
                    ])
            )
            ->when(
                $this->excludeSoftDeletedTorrents,
                fn ($query) => $query->whereDoesntHave('torrent', fn ($query) => $query->onlyTrashed()->withoutGlobalScope(ApprovedScope::class))
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.unregistered-info-hash-search', [
            'unregisteredInfoHashes' => $this->unregisteredInfoHashes,
        ]);
    }
}
