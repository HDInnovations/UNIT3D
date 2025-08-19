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

use App\Models\Group;
use App\Models\User;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public bool $show = true;

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $username = '';

    #[Url(history: true)]
    public string $soundexUsername = '';

    #[Url(history: true)]
    public string $email = '';

    #[Url(history: true)]
    public string $soundexEmail = '';

    #[Url(history: true)]
    public string $rsskey = '';

    #[Url(history: true)]
    public string $apikey = '';

    #[Url(history: true)]
    public string $passkey = '';

    #[Url(history: true)]
    public ?int $groupId = null;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    final public function updatingShow(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, User>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $users {
        get => User::query()
            ->with('group')
            ->when($this->username !== '', fn ($query) => $query->where('username', 'LIKE', '%'.$this->username.'%'))
            ->when(
                $this->soundexUsername !== '',
                fn ($query) => $query->whereRaw('SOUNDEX(username) = SOUNDEX(?)', [$this->soundexUsername]),
            )
            ->when($this->email !== '', fn ($query) => $query->where('email', 'LIKE', '%'.$this->email.'%'))
            ->when(
                $this->soundexEmail !== '',
                fn ($query) => $query->when(
                    str_contains($this->soundexEmail, '@'),
                    fn ($query) => $query->whereRaw('SOUNDEX(email) = SOUNDEX(?)', [$this->soundexEmail]),
                    fn ($query) => $query->whereRaw("SOUNDEX(SUBSTRING_INDEX(email, '@', 1)) = SOUNDEX(SUBSTRING_INDEX(?, '@', 1))", [$this->soundexEmail])
                )
            )
            ->when($this->rsskey !== '', fn ($query) => $query->where('rsskey', 'LIKE', '%'.$this->rsskey.'%'))
            ->when($this->apikey !== '', fn ($query) => $query->where('api_token', 'LIKE', '%'.$this->apikey.'%'))
            ->when($this->passkey !== '', fn ($query) => $query->where('passkey', 'LIKE', '%'.$this->passkey.'%'))
            ->when($this->groupId !== null, fn ($query) => $query->where('group_id', '=', $this->groupId))
            ->when($this->show === true, fn ($query) => $query->withTrashed())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * @var \Illuminate\Support\Collection<int, Group>
     */
    final protected $groups {
        get => Group::orderBy('position')->get();
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-search', [
            'users'  => $this->users,
            'groups' => $this->groups,
        ]);
    }
}
