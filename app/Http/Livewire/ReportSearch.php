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
    public ?string $judge = null;

    #[Url(history: true)]
    public ?string $title = null;

    #[Url(history: true)]
    public ?string $message = null;

    #[Url(history: true)]
    public ?string $verdict = null;

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
        get => Report::query()
            ->with('reported.group', 'reporter.group', 'assignee.group')
            ->when($this->type !== null, fn ($query) => $query->where('type', '=', $this->type))
            ->when($this->reporter !== null, fn ($query) => $query->whereRelation('reporter', 'username', 'LIKE', '%'.$this->reporter.'%'))
            ->when($this->reported !== null, fn ($query) => $query->whereRelation('reported', 'username', 'LIKE', '%'.$this->reported.'%'))
            ->when($this->staff !== null, fn ($query) => $query->whereRelation('assignee', 'username', 'LIKE', '%'.$this->staff.'%'))
            ->when($this->judge !== null, fn ($query) => $query->whereRelation('judge', 'username', 'LIKE', '%'.$this->judge.'%'))
            ->when($this->title !== null, fn ($query) => $query->where('title', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->title.'%')))
            ->when($this->message !== null, fn ($query) => $query->where('message', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->message.'%')))
            ->when($this->verdict !== null, fn ($query) => $query->where('verdict', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->verdict.'%')))
            ->when($this->status === 'open', fn ($query) => $query->whereNull('solved_by')->where(fn ($query) => $query->whereNull('snoozed_until')->orWhere('snoozed_until', '<', now())))
            ->when($this->status === 'snoozed', fn ($query) => $query->whereNull('solved_by')->where('snoozed_until', '>', now()))
            ->when($this->status === 'closed', fn ($query) => $query->whereNotNull('solved_by'))
            ->when($this->status === 'all_open', fn ($query) => $query->whereNull('solved_by'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(min($this->perPage, 100));
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.report-search', [
            'reports' => $this->reports,
        ]);
    }
}
