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

use App\Models\History;
use App\Traits\CastLivewireProperties;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LeakerSearch extends Component
{
    use CastLivewireProperties;
    use LivewireSort;
    use WithPagination;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 50;

    #[Url(history: true)]
    public string $torrentIds = '';

    #[Url(history: true)]
    public ?int $minutesLeakedWithin = null;

    #[Url(history: true)]
    public string $agent = '';

    #[Url(history: true)]
    public string $sortField = 'leak_count';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator<int, History>
     */
    final protected \Illuminate\Pagination\LengthAwarePaginator $leakers {
        get => History::query()
            ->select([
                'history.user_id',
                DB::raw('count(*) as leak_count'),
            ])
            ->with([
                'user' => [
                    'group',
                    'history' => fn ($query) => $query->select(['user_id', 'agent'])->distinct()->orderBy('agent'),
                    'peers'   => fn ($query) => $query->select(['user_id', 'ip'])->distinct()->orderBy('ip'),
                ],
            ])
            ->join('torrents', 'history.torrent_id', '=', 'torrents.id')
            ->whereIn('history.torrent_id', array_filter(array_map('trim', explode(',', $this->torrentIds))))
            ->when(
                $this->minutesLeakedWithin !== null,
                fn ($query) => $query->whereRaw('history.created_at < TIMESTAMPADD(MINUTE, ? , torrents.created_at)', [$this->minutesLeakedWithin])
            )
            ->whereColumn('history.user_id', '<>', 'torrents.user_id')
            ->groupBy('history.user_id')
            ->when($this->agent !== '', fn ($query) => $query->where('agent', 'LIKE', $this->agent))
            ->when($this->sortField !== '', fn ($query) => $query->orderBy($this->sortField, $this->sortDirection))
            ->paginate($this->perPage);
    }

    final protected int $torrentIdCount {
        get => \count(array_filter(array_map('trim', explode(',', $this->torrentIds))));
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.leaker-search', [
            'leakers'        => $this->leakers,
            'torrentIdCount' => $this->torrentIdCount,
        ]);
    }
}
