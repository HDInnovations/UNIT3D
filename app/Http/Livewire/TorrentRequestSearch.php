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

namespace App\Http\Livewire;

use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\TorrentRequestClaim;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TorrentRequestSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    public string $name = '';

    public string $requestor = '';

    /**
     * @var string[]
     */
    public array $categories = [];

    /**
     * @var string[]
     */
    public array $types = [];

    /**
     * @var string[]
     */
    public array $resolutions = [];

    /**
     * @var string[]
     */
    public array $genres = [];

    public ?int $tmdbId = null;

    public string $imdbId = '';

    public ?int $tvdbId = null;

    public ?int $malId = null;

    public bool $unfilled = false;

    public bool $claimed = false;

    public bool $pending = false;

    public bool $filled = false;

    public bool $myRequests = false;

    public bool $myClaims = false;

    public bool $myVoted = false;

    public bool $myFilled = false;

    public int $perPage = 25;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showFilters = false;

    /**
     * @var array<mixed>
     */
    protected $queryString = [
        'name'          => ['except' => ''],
        'requestor'     => ['except' => ''],
        'categories'    => ['except' => []],
        'types'         => ['except' => []],
        'resolutions'   => ['except' => []],
        'genres'        => ['except' => []],
        'tmdbId'        => ['except' => ''],
        'imdbId'        => ['except' => ''],
        'tvdbId'        => ['except' => ''],
        'malId'         => ['except' => ''],
        'unfilled'      => ['except' => false],
        'claimed'       => ['except' => false],
        'pending'       => ['except' => false],
        'filled'        => ['except' => false],
        'myRequests'    => ['except' => false],
        'myClaims'      => ['except' => false],
        'myVoted'       => ['except' => false],
        'myFilled'      => ['except' => false],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page'          => ['except' => 1],
    ];

    final public function updating(string $field, mixed &$value): void
    {
        $this->castLivewireProperties($field, $value);
    }

    final public function updatedPage(): void
    {
        $this->emit('paginationChanged');
    }

    final public function toggleShowFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    final public function getTorrentRequestStatProperty(): ?object
    {
        return DB::table('requests')
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when filled_by is not null then 1 end) as filled')
            ->selectRaw('count(case when filled_by is null then 1 end) as unfilled')
            ->first();
    }

    final public function getTorrentRequestBountyStatProperty(): ?object
    {
        return DB::table('requests')
            ->selectRaw('coalesce(sum(bounty), 0) as total')
            ->selectRaw('coalesce(sum(case when filled_by is not null then bounty end), 0) as claimed')
            ->selectRaw('coalesce(sum(case when filled_by is null then bounty end), 0) as unclaimed')
            ->first();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<TorrentRequest>
     */
    final public function getTorrentRequestsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $user = auth()->user();
        $isRegexAllowed = $user->group->is_modo;
        $isRegex = fn ($field) => $isRegexAllowed
            && \strlen((string) $field) > 2
            && $field[0] === '/'
            && $field[-1] === '/'
            && @preg_match($field, 'Validate regex') !== false;

        return TorrentRequest::with(['user:id,username,group_id', 'user.group', 'category', 'type', 'resolution'])
            ->withCount(['comments'])
            ->when($this->name !== '', fn ($query) => $query->ofName($this->name, $isRegex($this->name)))
            ->when($this->requestor !== '', fn ($query) => $query->ofUploader($this->requestor))
            ->when($this->categories !== [], fn ($query) => $query->ofCategory($this->categories))
            ->when($this->types !== [], fn ($query) => $query->ofType($this->types))
            ->when($this->resolutions !== [], fn ($query) => $query->ofResolution($this->resolutions))
            ->when($this->tmdbId !== null, fn ($query) => $query->ofTmdb($this->tmdbId))
            ->when($this->imdbId !== '', fn ($query) => $query->ofImdb((int) (preg_match('/tt0*(?=(\d{7,}))/', $this->imdbId, $matches) ? $matches[1] : $this->imdbId)))
            ->when($this->tvdbId !== null, fn ($query) => $query->ofTvdb((int) $this->tvdbId))
            ->when($this->malId !== null, fn ($query) => $query->ofMal((int) $this->malId))
            ->when($this->unfilled || $this->claimed || $this->pending || $this->filled, function ($query): void {
                $query->where(function ($query): void {
                    $query->where(function ($query): void {
                        if ($this->unfilled) {
                            $query->whereNull('torrent_id')->whereNull('claimed');
                        }
                    })
                        ->orWhere(function ($query): void {
                            if ($this->claimed) {
                                $query->whereNotNull('claimed')->whereNull('torrent_id')->whereNull('approved_by');
                            }
                        })
                        ->orWhere(function ($query): void {
                            if ($this->pending) {
                                $query->whereNotNull('torrent_id')->whereNull('approved_by');
                            }
                        })
                        ->orWhere(function ($query): void {
                            if ($this->filled) {
                                $query->whereNotNull('torrent_id')->whereNotNull('approved_by');
                            }
                        });
                });
            })
            ->when($this->myRequests, function ($query) use ($user): void {
                $query->where('user_id', '=', $user->id);
            })
            ->when($this->myClaims, function ($query) use ($user): void {
                $requestClaims = TorrentRequestClaim::where('user_id', '=', $user->id)->pluck('request_id');
                $query->whereIntegerInRaw('id', $requestClaims)->whereNull('torrent_id')->whereNull('approved_by');
            })
            ->when($this->myVoted, function ($query) use ($user): void {
                $requestVotes = TorrentRequestBounty::where('user_id', '=', $user->id)->pluck('requests_id');
                $query->whereIntegerInRaw('id', $requestVotes);
            })
            ->when($this->myFilled, function ($query) use ($user): void {
                $query->where('filled_by', '=', $user->id);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.torrent-request-search', [
            'user'                     => auth()->user(),
            'torrentRequests'          => $this->torrentRequests,
            'torrentRequestStat'       => $this->torrentRequestStat,
            'torrentRequestBountyStat' => $this->torrentRequestBountyStat,
        ]);
    }
}
