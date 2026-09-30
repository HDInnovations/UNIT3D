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

use App\Enums\ModerationStatus;
use App\Models\History;
use App\Models\User;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserTorrents extends Component
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
    public string $unsatisfied = 'any';

    #[Url(history: true)]
    public string $active = 'any';

    #[Url(history: true)]
    public string $completed = 'any';

    #[Url(history: true)]
    public string $uploaded = 'any';

    #[Url(history: true)]
    public string $hitrun = 'any';

    #[Url(history: true)]
    public string $prewarn = 'any';

    #[Url(history: true)]
    public string $immune = 'any';

    #[Url(history: true)]
    public string $downloaded = 'any';

    /**
     * @var string[]
     */
    #[Url(history: true)]
    public array $status = [];

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

    /**
     * @var string[]
     */
    private const array RESETTABLE_FILTERS = [
        'name',
        'unsatisfied',
        'active',
        'completed',
        'uploaded',
        'hitrun',
        'prewarn',
        'immune',
        'downloaded',
        'status',
        'perPage',
    ];

    final public function updated(string $property): void
    {
        $base = str_contains($property, '.') ? strstr($property, '.', true) : $property;

        if (\in_array($base, self::RESETTABLE_FILTERS, true)) {
            $this->resetPage();
        }
    }

    /**
     * Reset all filters (excluding sorting, perPage and precision) to their defaults.
     */
    final public function clearFilters(): void
    {
        $this->name = '';
        $this->unsatisfied = 'any';
        $this->active = 'any';
        $this->completed = 'any';
        $this->uploaded = 'any';
        $this->hitrun = 'any';
        $this->prewarn = 'any';
        $this->immune = 'any';
        $this->downloaded = 'any';
        $this->status = [];

        $this->resetPage();
    }

    /**
     * Remove a single filter (or a single value from the status filter) while
     * preserving the state of all other filters.
     */
    final public function removeFilter(string $filter, ?string $value = null): void
    {
        match ($filter) {
            'name' => $this->name = '',
            'unsatisfied' => $this->unsatisfied = 'any',
            'active' => $this->active = 'any',
            'completed' => $this->completed = 'any',
            'uploaded' => $this->uploaded = 'any',
            'hitrun' => $this->hitrun = 'any',
            'prewarn' => $this->prewarn = 'any',
            'immune' => $this->immune = 'any',
            'downloaded' => $this->downloaded = 'any',
            'status' => $this->status = array_values(
                array_filter($this->status, fn ($status): bool => (string) $status !== (string) $value)
            ),
            default => null,
        };

        $this->resetPage();
    }

    /**
     * The filters currently narrowing the list, as removable chips.
     *
     * @var list<array{filter: string, value: null|string, label: string}>
     */
    final protected array $activeFilters {
        get {
            $chips = [];

            if ($this->name !== '') {
                $chips[] = [
                    'filter' => 'name',
                    'value'  => null,
                    'label'  => trans('livewire-interface.history.search-chip', ['name' => $this->name]),
                ];
            }

            $labels = [
                'active'      => trans('livewire-interface.history.active'),
                'unsatisfied' => trans('livewire-interface.history.unsatisfied'),
                'completed'   => trans('livewire-interface.history.completed-filter'),
                'prewarn'     => trans('livewire-interface.history.prewarn'),
                'hitrun'      => trans('livewire-interface.history.hitrun'),
                'immune'      => trans('livewire-interface.history.immune'),
                'uploaded'    => trans('livewire-interface.history.uploaded-filter'),
                'downloaded'  => trans('livewire-interface.history.downloaded-filter'),
            ];

            foreach ($labels as $filter => $label) {
                if ($this->{$filter} === 'any') {
                    continue;
                }

                $chips[] = [
                    'filter' => $filter,
                    'value'  => null,
                    'label'  => $this->{$filter} === 'include'
                        ? $label
                        : trans('livewire-interface.history.exclude-chip', ['filter' => $label]),
                ];
            }

            $moderationLabels = [
                (string) ModerationStatus::PENDING->value   => trans('torrent.pending'),
                (string) ModerationStatus::APPROVED->value  => trans('torrent.approved'),
                (string) ModerationStatus::REJECTED->value  => trans('torrent.rejected'),
                (string) ModerationStatus::POSTPONED->value => trans('torrent.postponed'),
            ];

            foreach ($this->status as $status) {
                $chips[] = [
                    'filter' => 'status',
                    'value'  => (string) $status,
                    'label'  => $moderationLabels[(string) $status] ?? (string) $status,
                ];
            }

            return $chips;
        }
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, History>
     */
    final protected \Illuminate\Contracts\Pagination\LengthAwarePaginator $history {
        get => History::query()
            ->join(
                'torrents',
                fn ($join) => $join
                    ->on('history.torrent_id', '=', 'torrents.id')
                    ->where('history.user_id', '=', $this->user->id)
            )
            ->select(
                'history.torrent_id',
                'history.agent',
                'history.uploaded',
                'history.downloaded',
                'history.seeder',
                'history.active',
                'history.actual_uploaded',
                'history.actual_downloaded',
                'history.seedtime',
                'history.created_at',
                'history.updated_at',
                'history.completed_at',
                'history.immune',
                'history.hitrun',
                'history.prewarned_at',
                'torrents.name',
                'torrents.seeders',
                'torrents.leechers',
                'torrents.times_completed',
                'torrents.size',
                'torrents.user_id',
                'torrents.status',
                DB::raw('history.active AND history.seeder AS seeding'),
                DB::raw('history.active AND NOT history.seeder AS leeching'),
                DB::raw('NOT history.active AND history.seeder AS completed'),
            )
            ->selectRaw('torrents.user_id = ? AS uploader', [$this->user->id])
            ->selectRaw('TIMESTAMPDIFF(SECOND, history.created_at, history.completed_at) AS leechtime')
            ->selectRaw('CAST(history.uploaded AS float) / CAST((history.downloaded + 1) AS float) AS ratio')
            ->selectRaw('CAST(history.actual_uploaded AS float) / CAST((history.actual_downloaded + 1) AS float) AS actual_ratio')
            ->when(
                $this->name,
                fn ($query) => $query
                    ->where('name', 'like', '%'.str_replace(' ', '%', $this->name).'%')
            )
            ->when(
                $this->unsatisfied === 'exclude',
                fn ($query) => $query
                    ->where(
                        fn ($query) => $query
                            ->where('seedtime', '>', config('hitrun.seedtime'))
                            ->orWhere('immune', '=', 1)
                            ->orWhereRaw('actual_downloaded < (torrents.size * ? / 100)', [config('hitrun.buffer')])
                    )
            )
            ->when(
                $this->unsatisfied === 'include',
                fn ($query) => $query
                    ->where('seedtime', '<', config('hitrun.seedtime'))
                    ->where('immune', '=', 0)
                    ->whereRaw('actual_downloaded > (torrents.size * ? / 100)', [config('hitrun.buffer')])
            )
            ->when($this->active === 'include', fn ($query) => $query->where('active', '=', 1))
            ->when($this->active === 'exclude', fn ($query) => $query->where(fn ($query) => $query->where('active', '=', 0)->orWhereNull('active')))
            ->when($this->completed === 'include', fn ($query) => $query->where('seeder', '=', 1))
            ->when($this->completed === 'exclude', fn ($query) => $query->where(fn ($query) => $query->where('seeder', '=', 0)->orWhereNull('seeder')))
            ->when($this->prewarn === 'include', fn ($query) => $query->whereNotNull('prewarned_at'))
            ->when($this->prewarn === 'exclude', fn ($query) => $query->whereNull('prewarned_at'))
            ->when($this->hitrun === 'include', fn ($query) => $query->where('hitrun', '=', 1))
            ->when($this->hitrun === 'exclude', fn ($query) => $query->where(fn ($query) => $query->where('hitrun', '=', 0)->orWhereNull('hitrun')))
            ->when($this->immune === 'include', fn ($query) => $query->where('immune', '=', 1))
            ->when($this->immune === 'exclude', fn ($query) => $query->where(fn ($query) => $query->where('immune', '=', 0)->orWhereNull('immune')))
            ->when($this->uploaded === 'include', fn ($query) => $query->where('torrents.user_id', '=', $this->user->id))
            ->when($this->uploaded === 'exclude', fn ($query) => $query->where('torrents.user_id', '<>', $this->user->id))
            ->when($this->downloaded === 'include', fn ($query) => $query->where('history.actual_downloaded', '>', 0))
            ->when($this->downloaded === 'exclude', fn ($query) => $query->where('history.actual_downloaded', '=', 0))
            ->when(!empty($this->status), fn ($query) => $query->whereIntegerInRaw('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-torrents', [
            'histories'     => $this->history,
            'activeFilters' => $this->activeFilters,
        ]);
    }

    /**
     * Update History Immune.
     */
    final public function updateImmune(int $torrentId, bool $immune): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $this->user
            ->history()
            ->where('torrent_id', '=', $torrentId)
            ->update([
                'immune' => $immune,
            ]);

        $this->dispatch('success', type: 'success', message: __('application-messages.flash.immunity-updated'));
    }
}
