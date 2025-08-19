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

use App\Models\Ticket;
use App\Models\User;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TicketSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public bool $show = false;

    #[Url(history: true)]
    public ?User $user = null;

    #[Url(history: true)]
    public string $tab = 'open';

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sortField = 'updated_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function mount(): void
    {
        $this->user = auth()->user();
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    final public function updatingTab(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Ticket>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $tickets {
        get => Ticket::query()
            ->with(['user.group', 'staff.group', 'category', 'priority'])
            ->when(!$this->user->group->is_modo, fn ($query) => $query->where('user_id', '=', $this->user->id))
            ->when(
                $this->tab === 'open',
                fn ($query) => $query->whereNull('closed_at'),
                fn ($query) => $query->whereNotNull('closed_at')
            )
            ->when($this->search, fn ($query) => $query->where('subject', 'LIKE', '%'.$this->search.'%'))
            ->when($this->show === true, fn ($query) => $query->where('staff_id', '=', auth()->user()->id))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ticket-search', [
            'tickets' => $this->tickets,
        ]);
    }
}
