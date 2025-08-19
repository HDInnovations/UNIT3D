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

use App\Enums\ModerationStatus;
use App\Models\Application;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $email = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 25;

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Application>
     */
    protected \Illuminate\Pagination\LengthAwarePaginator $applications {
        get => Application::query()
            ->withoutGlobalScopes()
            ->with([
                'user.group',
                'moderated.group',
                'imageProofs',
                'urlProofs'
            ])
            ->when($this->email, fn ($query) => $query->where('email', 'LIKE', '%'.$this->email.'%'))
            ->when($this->status === '1', fn ($query) => $query->where('status', '=', ModerationStatus::APPROVED))
            ->when($this->status === '0', fn ($query) => $query->where('status', '=', ModerationStatus::PENDING))
            ->when($this->status === '2', fn ($query) => $query->where('status', '=', ModerationStatus::REJECTED))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function destroy(int $id): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $application = Application::withoutGlobalScopes()->findOrFail($id);
        $application->urlProofs()->delete();
        $application->imageProofs()->delete();
        $application->delete();

        $this->dispatch('success', type: 'success', message: 'Application has successfully been deleted!');
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.application-search', [
            'applications' => $this->applications
        ]);
    }
}
