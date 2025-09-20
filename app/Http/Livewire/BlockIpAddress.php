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

use App\Models\BlockedIp;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class BlockIpAddress extends Component
{
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Validate('required|filled|ip')]
    public string $ipAddress = '';

    #[Validate('required|filled|string')]
    public string $reason = '';

    #[Url(history: true)]
    public string $ipSearch = '';

    #[Url(history: true)]
    public string $reasonSearch = '';

    #[Url(history: true)]
    public int $perPage = 25;

    final public function store(): void
    {
        abort_unless(auth()->user()->group->is_modo, 403);

        $this->validate();

        BlockedIp::create([
            'ip_address' => $this->ipAddress,
            'reason'     => $this->reason,
            'user_id'    => auth()->user()->id,
        ]);

        $this->reason = '';

        cache()->forget('blocked-ips');

        $this->dispatch('success', type: 'success', message: 'Ip addresses successfully blocked!');
    }

    final public function destroy(BlockedIp $blockedIp): void
    {
        if (auth()->user()->group->is_modo) {
            $blockedIp->delete();

            $this->dispatch('success', type: 'success', message: 'IP has successfully been deleted!');
        } else {
            $this->dispatch('error', type: 'error', message: 'Permission denied!');
        }
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, BlockedIp>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $ipAddresses {
        get => BlockedIp::query()
            ->when($this->ipSearch, fn ($query) => $query->where('ip_address', 'LIKE', '%'.$this->ipSearch.'%'))
            ->when($this->reasonSearch, fn ($query) => $query->where('reason', 'LIKE', '%'.$this->reasonSearch.'%'))
            ->latest()
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.block-ip-address', [
            'ipAddresses' => $this->ipAddresses,
        ]);
    }
}
