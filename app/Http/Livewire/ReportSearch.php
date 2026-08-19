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

use App\Models\Report;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReportSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public ?string $reporter = null;

    #[Url(history: true)]
    public ?string $reported = null;

    #[Url(history: true)]
    public ?string $staff = null;

    #[Url(history: true)]
    public ?string $title = null;

    #[Url(history: true)]
    public ?string $message = null;

    #[Url(history: true)]
    public ?string $reply = null;

    #[Url(history: true)]
    public ?string $type = null;

    #[Url(history: true)]
    public ?string $status = 'open';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 25;

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Report>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $reports {
        get {
            if (!$this->isModo && $this->sortField === 'reported_user_id') {
                $this->reset('sortField');
            }

            return Report::query()
                ->with('reported.group', 'reporter.group', 'staff.group')
                ->when(!auth()->user()->group->is_modo, fn ($query) => $query->where('reporter_id', '=', auth()->id()))
                ->when($this->type !== null, fn ($query) => $query->where('type', '=', $this->type))
                ->when($this->reporter !== null, fn ($query) => $query->whereRelation('reporter', 'username', 'LIKE', '%'.$this->reporter.'%'))
                ->when($this->reported !== null, fn ($query) => $query->whereRelation('reported', 'username', 'LIKE', '%'.$this->reported.'%'))
                ->when($this->staff !== null, fn ($query) => $query->whereRelation('staff', 'username', 'LIKE', '%'.$this->staff.'%'))
                ->when($this->title !== null, fn ($query) => $query->where('title', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->title.'%')))
                ->when($this->message !== null, fn ($query) => $query->where('message', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->message.'%')))
                ->when($this->reply !== null, fn ($query) => $query->whereRelation('replies', 'content', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->reply.'%')))
                ->when($this->status === 'open', fn ($query) => $query->whereNull('solved_at')->where(fn ($query) => $query->whereNull('snoozed_until')->orWhere('snoozed_until', '<', now())))
                ->when($this->status === 'snoozed', fn ($query) => $query->whereNull('solved_at')->where('snoozed_until', '>', now()))
                ->when($this->status === 'closed', fn ($query) => $query->whereNotNull('solved_at'))
                ->when($this->status === 'all_open', fn ($query) => $query->whereNull('solved_at'))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(min($this->perPage, 100));
        }
    }

    final protected bool $isModo {
        get => auth()->user()->group->is_modo;
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.report-search', [
            'reports' => $this->reports,
            'isModo'  => $this->isModo,
        ]);
    }
}
