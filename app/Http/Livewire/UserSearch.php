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
use App\Models\FailedLoginAttempt;
use App\Models\Group;
use App\Models\Note;
use App\Models\Peer;
use App\Models\Seedbox;
use App\Models\User;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    public string $ipAddress = '';

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

    final public function updatingIpAddress(): void
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
            ->paginate(min($this->perPage, 100));
    }

    /**
     * @var Collection<int, Group>
     */
    final protected $groups {
        get => Group::query()->orderBy('position')->get();
    }

    /**
     * @var Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    final protected Collection $ipMatches {
        get {
            $ipAddress = trim($this->ipAddress);

            if ($ipAddress === '') {
                return collect();
            }

            return collect()
                ->concat($this->peerIpMatches($ipAddress))
                ->concat($this->sessionIpMatches($ipAddress))
                ->concat($this->failedLoginIpMatches($ipAddress))
                ->concat($this->blockedIpMatches($ipAddress))
                ->concat($this->seedboxIpMatches($ipAddress))
                ->concat($this->userNoteIpMatches($ipAddress));
        }
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function peerIpMatches(string $ipAddress): Collection
    {
        return Peer::query()
            ->select(['peers.user_id'])
            ->selectRaw('INET6_NTOA(peers.ip) as ip_address')
            ->selectRaw('COUNT(*) as result_count')
            ->selectRaw('MAX(peers.updated_at) as latest_activity_at')
            ->with(['user.group'])
            ->where(DB::raw('INET6_NTOA(peers.ip)'), 'LIKE', $ipAddress.'%')
            ->groupBy(['peers.user_id', 'peers.ip'])
            ->orderByDesc('latest_activity_at')
            ->limit(50)
            ->get()
            ->map(fn (Peer $peer): array => [
                'source'        => 'Peers',
                'user'          => $peer->user,
                'fallback_user' => null,
                'ip_address'    => $peer->ip_address,
                'details'       => $peer->result_count.' peer '.Str::plural('record', (int) $peer->result_count),
                'matched_at'    => $peer->latest_activity_at,
            ]);
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function sessionIpMatches(string $ipAddress): Collection
    {
        $sessions = DB::table('sessions')
            ->select(['id', 'user_id', 'ip_address', 'user_agent', 'last_activity'])
            ->where('ip_address', 'LIKE', $ipAddress.'%')
            ->orderByDesc('last_activity')
            ->limit(50)
            ->get();

        $users = User::query()
            ->with('group')
            ->whereKey($sessions->pluck('user_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return $sessions->map(fn (object $session): array => [
            'source'        => 'Sessions',
            'user'          => $users->get($session->user_id),
            'fallback_user' => $session->user_id === null ? 'Guest' : null,
            'ip_address'    => $session->ip_address,
            'details'       => Str::limit((string) $session->user_agent, 160),
            'matched_at'    => Carbon::createFromTimestamp((int) $session->last_activity)->toDateTimeString(),
        ]);
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function failedLoginIpMatches(string $ipAddress): Collection
    {
        return FailedLoginAttempt::query()
            ->select(['user_id', 'username', 'ip_address'])
            ->selectRaw('COUNT(*) as result_count')
            ->selectRaw('MAX(created_at) as latest_activity_at')
            ->with(['user.group'])
            ->where('ip_address', 'LIKE', $ipAddress.'%')
            ->groupBy(['user_id', 'username', 'ip_address'])
            ->orderByDesc('latest_activity_at')
            ->limit(50)
            ->get()
            ->map(fn (FailedLoginAttempt $failedLoginAttempt): array => [
                'source'        => 'Failed logins',
                'user'          => $failedLoginAttempt->user,
                'fallback_user' => $failedLoginAttempt->username,
                'ip_address'    => $failedLoginAttempt->ip_address,
                'details'       => $failedLoginAttempt->result_count.' failed login '.Str::plural('attempt', (int) $failedLoginAttempt->result_count),
                'matched_at'    => $failedLoginAttempt->latest_activity_at,
            ]);
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function blockedIpMatches(string $ipAddress): Collection
    {
        return BlockedIp::query()
            ->with(['user.group'])
            ->where('ip_address', 'LIKE', $ipAddress.'%')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (BlockedIp $blockedIp): array => [
                'source'        => 'Blocked IPs',
                'user'          => $blockedIp->user,
                'fallback_user' => null,
                'ip_address'    => $blockedIp->ip_address,
                'details'       => Str::limit((string) $blockedIp->reason, 160),
                'matched_at'    => $blockedIp->created_at,
            ]);
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function seedboxIpMatches(string $ipAddress): Collection
    {
        return Seedbox::query()
            ->with(['user.group'])
            ->latest()
            ->get()
            ->filter(fn (Seedbox $seedbox): bool => Str::startsWith($seedbox->ip, $ipAddress))
            ->take(50)
            ->values()
            ->map(fn (Seedbox $seedbox): array => [
                'source'        => 'Seedboxes',
                'user'          => $seedbox->user,
                'fallback_user' => null,
                'ip_address'    => $seedbox->ip,
                'details'       => $seedbox->name,
                'matched_at'    => $seedbox->created_at,
            ]);
    }

    /**
     * @return Collection<int, array{source: string, user: User|null, fallback_user: string|null, ip_address: string, details: string, matched_at: mixed}>
     */
    private function userNoteIpMatches(string $ipAddress): Collection
    {
        return Note::query()
            ->with(['user.group', 'staff'])
            ->where('message', 'LIKE', '%'.$ipAddress.'%')
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Note $note): array => [
                'source'        => 'User notes',
                'user'          => $note->user,
                'fallback_user' => null,
                'ip_address'    => $ipAddress,
                'details'       => 'By '.$note->staff->username.': '.Str::limit($note->message, 160),
                'matched_at'    => $note->created_at,
            ]);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-search', [
            'users'     => $this->users,
            'groups'    => $this->groups,
            'ipMatches' => $this->ipMatches,
        ]);
    }
}
