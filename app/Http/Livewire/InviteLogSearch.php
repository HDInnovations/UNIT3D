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

use App\Models\Invite;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class InviteLogSearch extends Component
{
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public string $sender = '';

    #[Url(history: true)]
    public string $soundexSender = '';

    #[Url(history: true)]
    public string $email = '';

    #[Url(history: true)]
    public string $soundexEmail = '';

    #[Url(history: true)]
    public string $code = '';

    #[Url(history: true)]
    public string $receiver = '';

    #[Url(history: true)]
    public string $soundexReceiver = '';

    #[Url(history: true)]
    public string $custom = '';

    #[Url(history: true)]
    public string $groupBy = 'none';

    #[Url(history: true)]
    public int $threshold = 25;

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public int $perPage = 25;

    final public function mount(): void
    {
        $this->sortField = match ($this->groupBy) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    final public function updatingGroupBy(string $value): void
    {
        $this->sortField = match ($value) {
            'user_id' => 'created_at_max',
            default   => 'created_at',
        };
    }

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, Invite>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $invites {
        get => Invite::withTrashed()
            ->with([
                'sender'   => fn ($query) => $query->withTrashed()->with('group'),
                'receiver' => fn ($query) => $query->withTrashed()->with('group'),
            ])
            ->when(
                $this->sender || $this->soundexSender,
                fn ($query) => $query->whereHas(
                    'sender',
                    fn ($query) => $query
                        ->withTrashed()
                        ->when($this->sender, fn ($query) => $query->where('username', 'LIKE', $this->sender))
                        ->when($this->soundexSender, fn ($query) => $query->whereRaw('SOUNDEX(username) = SOUNDEX(?)', [$this->soundexSender]))
                )
            )
            ->when($this->email, fn ($query) => $query->where('email', 'LIKE', '%'.$this->email.'%'))
            ->when(
                $this->soundexEmail !== '',
                fn ($query) => $query->when(
                    str_contains($this->soundexEmail, '@'),
                    fn ($query) => $query->whereRaw('SOUNDEX(email) = SOUNDEX(?)', [$this->soundexEmail]),
                    fn ($query) => $query->whereRaw("SOUNDEX(SUBSTRING_INDEX(email, '@', 1)) = SOUNDEX(SUBSTRING_INDEX(?, '@', 1))", [$this->soundexEmail])
                )
            )
            ->when($this->code, fn ($query) => $query->where('code', 'LIKE', '%'.$this->code.'%'))
            ->when(
                $this->receiver || $this->soundexReceiver,
                fn ($query) => $query->whereHas(
                    'receiver',
                    fn ($query) => $query
                        ->withTrashed()
                        ->when($this->receiver, fn ($query) => $query->where('username', 'LIKE', $this->receiver))
                        ->when($this->soundexReceiver, fn ($query) => $query->whereRaw('SOUNDEX(username) = SOUNDEX(?)', [$this->soundexReceiver]))
                )
            )
            ->when($this->custom, fn ($query) => $query->where('custom', 'LIKE', '%'.$this->custom.'%'))
            ->when(
                $this->groupBy === 'user_id',
                fn ($query) => $query->groupBy('user_id')
                    ->select([
                        'user_id',
                        DB::raw('MIN(created_at) as created_at_min'),
                        DB::raw('FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(created_at))) as created_at_avg'),
                        DB::raw('MAX(created_at) as created_at_max'),
                        DB::raw('COUNT(*) as sent_count'),
                        DB::raw('SUM(accepted_by IS NOT NULL) as accepted_by_count'),
                        DB::raw("
                            (select
                                count(*)
                            from
                                users
                            where
                                id in (select accepted_by from invites i2 where i2.user_id = invites.user_id)
                                and group_id in (select id from `groups` where slug in ('banned', 'pruned', 'disabled'))
                            ) as inactive_count
                        "),
                        DB::raw("
                            100.0 *
                            (select
                                count(*)
                            from
                                users
                            where
                                id in (select accepted_by from invites i2 where i2.user_id = invites.user_id)
                                and group_id in (select id from `groups` where slug in ('banned', 'pruned', 'disabled'))
                            )
                            / COUNT(*) as inactive_ratio
                        "),
                    ])
                    ->withCasts([
                        'created_at_min' => 'datetime',
                        'created_at_avg' => 'datetime',
                        'created_at_max' => 'datetime',
                    ])
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.invite-log-search', [
            'invites' => $this->invites,
        ]);
    }
}
