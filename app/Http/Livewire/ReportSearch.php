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
use App\Models\User;
use App\Traits\LivewireSort;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property \Illuminate\Pagination\LengthAwarePaginator<int, Report> $reports
 */
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
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Report>
     */
    #[Computed]
    final public function reports(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Report::orderBy('solved')
            ->withCount(['comments'])
            ->with('reported.group', 'reporter.group', 'staff.group')
            ->when($this->type !== null, fn ($query) => $query->where('type', '=', $this->type))
            ->when($this->reporter !== null, fn ($query) => $query->whereIn('reporter_id', User::withTrashed()->select('id')->where('username', 'LIKE', '%'.$this->reporter.'%')))
            ->when($this->reported !== null, fn ($query) => $query->whereIn('reported_user', User::withTrashed()->select('id')->where('username', 'LIKE', '%'.$this->reported.'%')))
            ->when($this->staff !== null, fn ($query) => $query->whereIn('staff_id', User::withTrashed()->select('id')->where('username', 'LIKE', '%'.$this->staff.'%')))
            ->when($this->title !== null, fn ($query) => $query->where('title', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->title.'%')))
            ->when($this->message !== null, fn ($query) => $query->where('message', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->message.'%')))
            ->when($this->verdict !== null, fn ($query) => $query->where('verdict', 'LIKE', '%'.str_replace(' ', '%', '%'.$this->verdict.'%')))
            ->when($this->status === 'open', fn ($query) => $query->where('solved', '=', false)->where(fn ($query) => $query->whereNull('snoozed_until')->orWhere('snoozed_until', '<', now())))
            ->when($this->status === 'snoozed', fn ($query) => $query->where('solved', '=', false)->where('snoozed_until', '>', now()))
            ->when($this->status === 'closed', fn ($query) => $query->where('solved', '=', true))
            ->when($this->status === 'all_open', fn ($query) => $query->where('solved', '=', false))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.report-search', [
            'reports' => $this->reports,
        ]);
    }
}
