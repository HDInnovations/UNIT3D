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

use App\Models\EmailUpdate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\LivewireSort;

class EmailUpdateSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $groupBy = 'none';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 25;

    final public function mount(): void
    {
        $this->sortField = match ($this->groupBy) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    final public function updatingGroupBy(string $value): void
    {
        $this->sortField = match ($value) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, EmailUpdate>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $emailUpdates {
        get => EmailUpdate::query()
            ->with([
                'user' => fn ($query) => $query->withTrashed()->with('group'),
            ])
            ->when($this->username, fn ($query) => $query->whereIn('user_id', User::query()->withTrashed()->select('id')->where('username', 'LIKE', '%'.$this->username.'%')))
            ->when(
                $this->groupBy === 'user_id',
                fn ($query) => $query->groupBy('user_id')
                    ->select([
                        'user_id',
                        DB::raw('MIN(created_at) as created_at_min'),
                        DB::raw('FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(created_at))) as created_at_avg'),
                        DB::raw('MAX(created_at) as created_at_max'),
                        DB::raw('COUNT(*) as email_update_count'),
                        DB::raw('SUM(deleted_at IS NULL) as active_count'),
                        DB::raw('SUM(deleted_at IS NOT NULL) as deleted_count'),
                    ])
                    ->withCasts([
                        'created_at_min' => 'datetime',
                        'created_at_avg' => 'datetime',
                        'created_at_max' => 'datetime',
                    ])
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(min($this->perPage, 100));
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-update-search', [
            'emailUpdates' => $this->emailUpdates,
        ]);
    }
}
