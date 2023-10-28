<?php
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

namespace App\Livewire;

use App\Models\FailedLoginAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class FailedLoginSearch extends Component
{
    use WithPagination;

    public string $username = '';

    public string $userId = '';

    public string $ipAddress = '';

    public int $perPage = 25;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'username',
        'userId',
        'ipAddress',
        'page',
        'perPage',
    ];

    final public function updatedPage(): void
    {
        $this->dispatch('paginationChanged');
    }

    #[Computed]
    final public function failedLoginsTop10Ip(): \Illuminate\Database\Eloquent\Collection
    {
        return FailedLoginAttempt::query()
            ->select(['ip_address', DB::raw('COUNT(*) as login_attempts'), DB::raw('MAX(created_at) as latest_created_at')])
            ->groupBy('ip_address')
            ->having('login_attempts', '>', '3')
            ->having('latest_created_at', '>=', Carbon::now()->subWeek())
            ->orderByDesc('login_attempts')
            ->limit(10)
            ->get();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<FailedLoginAttempt>
     */
    #[Computed]
    final public function failedLogins(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return FailedLoginAttempt::query()
            ->with('user.group')
            ->when($this->username, fn ($query) => $query->where('username', 'LIKE', $this->username.'%'))
            ->when($this->userId, fn ($query) => $query->where('user_id', '=', $this->userId))
            ->when($this->ipAddress, fn ($query) => $query->where('ip_address', 'LIKE', $this->ipAddress.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.failed-login-search', [
            'failedLogins'        => $this->failedLogins,
            'failedLoginsTop10Ip' => $this->failedLoginsTop10Ip,
        ]);
    }
}
