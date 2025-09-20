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

use App\Models\User;
use App\Models\Warning;
use App\Notifications\WarningCreated;
use App\Notifications\WarningDeactivated;
use App\Notifications\WarningsDeactivated;
use App\Notifications\WarningsDeleted;
use App\Notifications\WarningTorrentDeleted;
use App\Traits\LivewireSort;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class UserWarnings extends Component
{
    use LivewireSort;
    use LivewireSort;
    use WithPagination;

    public User $user;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $warningTab = 'automated';

    #[Url(history: true)]
    #[Validate('required|filled|max:255')]
    public string $message = '';

    #[Url(history: true)]
    public int $perPage = 10;

    #[Url(history: true)]
    public ?string $sortField = null;

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Warning>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $warnings {
        get => $this->user
            ->userwarning()
            ->when(
                auth()->user()->group->is_modo,
                fn ($query) => $query->with('warneduser', 'staffuser', 'torrenttitle'),
                fn ($query) => $query->with('warneduser', 'torrenttitle'),
            )
            ->when($this->warningTab === 'automated', fn ($query) => $query->whereNotNull('torrent'))
            ->when($this->warningTab === 'manual', fn ($query) => $query->whereNull('torrent'))
            ->when($this->warningTab === 'deleted', fn ($query) => $query->onlyTrashed())
            ->when(
                $this->sortField === null,
                fn ($query) => $query->orderByDesc('active')->orderByDesc('created_at'),
                fn ($query) => $query->orderBy($this->sortField, $this->sortDirection),
            )
            ->paginate($this->perPage);
    }

    final protected int $automatedWarningsCount {
        get => $this->user->userwarning()->whereNotNull('torrent')->count();
    }

    final protected int $manualWarningsCount {
        get => $this->user->userwarning()->whereNull('torrent')->count();
    }

    final protected int $deletedWarningsCount {
        get => $this->user->userwarning()->onlyTrashed()->count();
    }

    /**
     * Manually warn a user.
     */
    final public function store(): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $this->validate();

        Warning::create([
            'user_id'    => $this->user->id,
            'warned_by'  => auth()->user()->id,
            'torrent'    => null,
            'reason'     => $this->message,
            'expires_on' => Carbon::now()->addDays(config('hitrun.expire')),
            'active'     => true,
        ]);

        $this->user->notify(new WarningCreated($this->message));

        $this->message = '';

        $this->dispatch('success', type: 'success', message: 'Warning issued successfully!');
    }

    /**
     * Deactivate A Warning.
     */
    final public function deactivate(Warning $warning): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $staff = auth()->user();

        $warning->update([
            'expires_on' => now(),
            'active'     => false,
        ]);

        $this->user->notify(new WarningDeactivated($staff, $warning));

        $this->dispatch('success', type: 'success', message: 'Warning was successfully deactivated');
    }

    /**
     * Reactivate A Warning.
     */
    final public function reactivate(Warning $warning): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $warning->update([
            'expires_on' => $warning->created_at->addDays(config('hitrun.expire')),
            'active'     => true,
        ]);

        $this->dispatch('success', type: 'success', message: 'Warning was successfully reactivated');
    }

    /**
     * Deactivate All Warnings.
     */
    final public function massDeactivate(): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $staff = auth()->user();

        $this->user
            ->warnings()
            ->where('active', '=', 1)
            ->update([
                'expires_on' => now(),
                'active'     => false,
            ]);

        $this->user->notify(new WarningsDeactivated($staff));

        $this->dispatch('success', type: 'success', message: 'All warnings were successfully deactivated');
    }

    /**
     * Delete A Warning.
     */
    final public function destroy(Warning $warning): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $staff = auth()->user();

        $warning->update([
            'deleted_by' => $staff->id,
            'active'     => false,
            'expires_on' => now(),
        ]);

        $warning->delete();

        $this->user->notify(new WarningTorrentDeleted($staff, $warning));

        $this->dispatch('success', type: 'success', message: 'Warning was successfully deleted');
    }

    /**
     * Delete All Warnings.
     */
    final public function massDestroy(): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $staff = auth()->user();

        $this->user->warnings()->update([
            'deleted_by' => $staff->id,
            'active'     => false,
            'expires_on' => now(),
        ]);

        $this->user->warnings()->delete();

        $this->user->notify(new WarningsDeleted($staff));

        $this->dispatch('success', type: 'success', message: 'All warnings were successfully deleted');
    }

    /**
     * Restore A Soft Deleted Warning.
     */
    final public function restore(int $id): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        Warning::withTrashed()->findOrFail($id)->restore();

        $this->dispatch('success', type: 'success', message: 'Warning was successfully restored');
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-warnings', [
            'warnings'               => $this->warnings,
            'automatedWarningsCount' => $this->automatedWarningsCount,
            'manualWarningsCount'    => $this->manualWarningsCount,
            'deletedWarningsCount'   => $this->deletedWarningsCount,
        ]);
    }
}
