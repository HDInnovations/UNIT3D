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

use App\Models\FailedLoginAttempt;
use App\Traits\LivewireSort;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class FailedLoginSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $userId = '';

    #[Url(history: true)]
    public string $ipAddress = '';

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, FailedLoginAttempt>
     */
    final protected \Illuminate\Database\Eloquent\Collection $failedLoginsTop10Ip {
        get => FailedLoginAttempt::query()
            ->select(['ip_address', DB::raw('COUNT(*) as login_attempts'), DB::raw('MAX(created_at) as latest_created_at')])
            ->groupBy('ip_address')
            ->having('login_attempts', '>', 3)
            ->having('latest_created_at', '>=', Carbon::now()->subWeek())
            ->orderByDesc('login_attempts')
            ->limit(10)
            ->get();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, FailedLoginAttempt>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $failedLogins {
        get => FailedLoginAttempt::query()
            ->with('user.group')
            ->when($this->username, fn ($query) => $query->where('username', 'LIKE', $this->username.'%'))
            ->when($this->userId, fn ($query) => $query->where('user_id', '=', $this->userId))
            ->when($this->ipAddress, fn ($query) => $query->where('ip_address', 'LIKE', $this->ipAddress.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.failed-login-search', [
            'failedLogins'        => $this->failedLogins,
            'failedLoginsTop10Ip' => $this->failedLoginsTop10Ip,
        ]);
    }
}
