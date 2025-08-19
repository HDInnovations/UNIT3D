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

use App\Models\Warning;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WarningLogSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $sender = '';

    #[Url(history: true)]
    public string $receiver = '';

    #[Url(history: true)]
    public string $torrent = '';

    #[Url(history: true)]
    public string $reason = '';

    #[Url(history: true)]
    public bool $show = false;

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Warning>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $warnings {
        get => Warning::query()
            ->with(['warneduser.group', 'staffuser.group', 'torrenttitle'])
            ->when($this->sender, fn ($query) => $query->whereRelation('staffuser', 'username', '=', $this->sender))
            ->when($this->receiver, fn ($query) => $query->whereRelation('warneduser', 'username', '=', $this->receiver))
            ->when($this->torrent, fn ($query) => $query->whereRelation('torrenttitle', 'name', 'LIKE', '%'.$this->torrent.'%'))
            ->when($this->reason, fn ($query) => $query->where('reason', 'LIKE', '%'.$this->reason.'%'))
            ->when($this->show === true, fn ($query) => $query->onlyTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warning-log-search', [
            'warnings' => $this->warnings,
        ]);
    }
}
