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

use App\Models\History;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class HistorySearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $agent = '';

    #[Url(history: true)]
    public string $torrent = '';

    #[Url(history: true)]
    public string $user = '';

    #[Url(history: true)]
    public string $seeder = 'any';

    #[Url(history: true)]
    public string $active = 'any';

    #[Url(history: true)]
    public string $groupBy = 'none';

    #[Url(history: true)]
    public string $sortField = '';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function updatingUser(): void
    {
        $this->resetPage();
    }

    final public function updatingAgent(): void
    {
        $this->resetPage();
    }

    final public function updatingTorrent(): void
    {
        $this->resetPage();
    }

    final public function updatingSeeder(): void
    {
        $this->resetPage();
    }

    final public function updatingActive(): void
    {
        $this->resetPage();
    }

    final public function updatingGroupBy(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, History>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $histories {
        get => History::query()
            ->with('user.group', 'torrent:id,name')
            ->when(
                $this->groupBy === 'user_id',
                fn ($query) => $query->groupBy('user_id')
                    ->select([
                        'user_id',
                        DB::raw('COUNT(*) AS torrent_count'),
                        DB::raw('SUM(uploaded) AS uploaded_sum'),
                        DB::raw('SUM(actual_uploaded) AS actual_uploaded_sum'),
                        DB::raw('SUM(client_uploaded) AS client_uploaded_sum'),
                        DB::raw('SUM(downloaded) AS downloaded_sum'),
                        DB::raw('SUM(actual_downloaded) AS actual_downloaded_sum'),
                        DB::raw('SUM(client_downloaded) AS client_downloaded_sum'),
                        DB::raw('SUM(refunded_download) AS refunded_download_sum'),
                        DB::raw('AVG(seedtime) AS seedtime_avg'),
                        DB::raw('MIN(created_at) AS created_at_min'),
                        DB::raw('MAX(updated_at) AS updated_at_max'),
                        DB::raw('SUM(active AND seeder) AS seeding_count'),
                        DB::raw('SUM(active AND NOT seeder) AS leeching_count'),
                        DB::raw('SUM(prewarned_at IS NOT NULL) AS prewarn_count'),
                        DB::raw('SUM(hitrun = TRUE) AS hitrun_count'),
                        DB::raw('SUM(immune = TRUE) AS immune_count'),
                    ])
                    ->withCasts([
                        'created_at_min' => 'datetime',
                        'updated_at_max' => 'datetime',
                    ]),
                fn ($query) => $query
                    ->select([
                        'user_id',
                        'torrent_id',
                        'uploaded',
                        'actual_uploaded',
                        'client_uploaded',
                        'downloaded',
                        'actual_downloaded',
                        'client_downloaded',
                        'refunded_download',
                        'seedtime',
                        'created_at',
                        'updated_at',
                        'completed_at',
                        DB::raw('active AND seeder AS seeding'),
                        DB::raw('active AND NOT seeder AS leeching '),
                        'prewarned_at',
                        'hitrun',
                        'immune',
                    ])
            )
            ->when($this->torrent !== '', fn ($query) => $query->whereRelation('torrent', 'name', 'LIKE', '%'.str_replace(' ', '%', $this->torrent).'%'))
            ->when($this->user !== '', fn ($query) => $query->whereRelation('user', 'username', 'LIKE', $this->user))
            ->when($this->agent !== '', fn ($query) => $query->where('history.agent', 'LIKE', $this->agent.'%'))
            ->when($this->active === 'include', fn ($query) => $query->where('active', '=', true))
            ->when($this->active === 'exclude', fn ($query) => $query->where('active', '=', false))
            ->when($this->seeder === 'include', fn ($query) => $query->where('seeder', '=', true))
            ->when($this->seeder === 'exclude', fn ($query) => $query->where('seeder', '=', false))
            ->when($this->sortField !== '', fn ($query) => $query->orderBy($this->sortField, $this->sortDirection))
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.history-search', [
            'histories' => $this->histories,
        ]);
    }
}
