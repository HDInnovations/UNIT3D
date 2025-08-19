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

use App\Models\BonEarning;
use App\Models\User;
use App\Models\Peer;
use App\Traits\LivewireSort;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserEarnings extends Component
{
    use LivewireSort;
    use WithPagination;

    public ?User $user = null;

    #TODO: Update URL attributes once Livewire 3 fixes upstream bug. See: https://github.com/livewire/livewire/discussions/7746

    #[Url(history: true)]
    public int $perPage = 25;

    #[Url(history: true)]
    public string $torrentName = '';

    #[Url(history: true)]
    public string $sortField = 'hourly_earnings';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    #[Url(history: true)]
    public bool $showMorePrecision = false;

    final public function mount(int $userId): void
    {
        $this->user = User::find($userId);
    }

    final public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @var \Illuminate\Database\Eloquent\Collection<int, BonEarning>
     */
    final protected \Illuminate\Support\Collection $bonEarnings {
        get {
            $outerQuery = DB::query()->select(DB::raw(1));
            $innerQuery = Peer::query()
                ->select(DB::raw(1))
                ->join('history', fn ($join) => $join->on('history.torrent_id', '=', 'peers.torrent_id')->on('history.user_id', '=', 'peers.user_id'))
                ->join('torrents', 'peers.torrent_id', '=', 'torrents.id')
                ->where('peers.seeder', '=', true)
                ->where('peers.active', '=', true)
                ->where('peers.user_id', '=', $this->user->id)
                ->where('peers.created_at', '<', now()->subMinutes(30))
                ->where('torrents.name', 'LIKE', '%'.str_replace(' ', '%', $this->torrentName).'%')
                ->groupBy('peers.torrent_id');

            foreach (BonEarning::with('conditions')->orderBy('position')->get() as $bonEarning) {
                // Raw bindings are fine since all database values are either enums or numeric
                $conditionQuery = '1=1';

                foreach ($bonEarning->conditions as $condition) {
                    $conditionQuery .= ' AND '.match ($condition->operand1) {
                        '1'                => '1',
                        'age'              => 'TIMESTAMPDIFF(SECOND, torrents.created_at, NOW())',
                        'size'             => 'torrents.size',
                        'seeders'          => 'torrents.seeders',
                        'leechers'         => 'torrents.leechers',
                        'times_completed'  => 'torrents.times_completed',
                        'internal'         => 'torrents.internal',
                        'personal_release' => 'torrents.personal_release',
                        'type_id'          => 'torrents.type_id',
                        'seedtime'         => 'history.seedtime',
                        'connectable'      => 'peers.connectable',
                    }.' '.$condition->operator.' '.$condition->operand2;
                }

                $innerQuery->selectRaw("MAX({$conditionQuery}) AS bon_earning_{$bonEarning->id}");
                $outerQuery->selectRaw("SUM(bon_earning_{$bonEarning->id}) AS bon_earning_{$bonEarning->id}");
            }

            $torrentCounts = $outerQuery->fromSub($innerQuery, 'peers_per_torrent')->first();

            return BonEarning::query()
                ->orderBy('position')
                ->get()
                ->map(function ($bonEarning) use ($torrentCounts) {
                    $bonEarning->setAttribute('torrents_count', $torrentCounts->{"bon_earning_{$bonEarning->id}"});

                    return $bonEarning;
                });
        }
    }

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    final protected \Illuminate\Database\Query\Builder $query {
        get {
            $bonEarnings = BonEarning::with('conditions')->orderBy('position')->get();

            $earningsQuery = str_repeat('(', $bonEarnings->count()).'0';

            foreach ($bonEarnings as $bonEarning) {
                // Raw bindings are fine since all database values are either enums or numeric
                $conditionQuery = '1=1';

                foreach ($bonEarning->conditions as $condition) {
                    $conditionQuery .= ' AND '.match ($condition->operand1) {
                        '1'                => '1',
                        'age'              => 'TIMESTAMPDIFF(SECOND, torrents.created_at, NOW())',
                        'size'             => 'torrents.size',
                        'seeders'          => 'torrents.seeders',
                        'leechers'         => 'torrents.leechers',
                        'times_completed'  => 'torrents.times_completed',
                        'internal'         => 'torrents.internal',
                        'personal_release' => 'torrents.personal_release',
                        'type_id'          => 'torrents.type_id',
                        'seedtime'         => 'history.seedtime',
                        'connectable'      => 'MAX(peers.connectable)',
                    }.' '.$condition->operator.' '.$condition->operand2;
                }

                $variable = match ($bonEarning->variable) {
                    '1'                => '1',
                    'age'              => 'TIMESTAMPDIFF(SECOND, torrents.created_at, NOW())',
                    'size'             => 'torrents.size',
                    'seeders'          => 'torrents.seeders',
                    'leechers'         => 'torrents.leechers',
                    'times_completed'  => 'torrents.times_completed',
                    'internal'         => 'torrents.internal',
                    'personal_release' => 'torrents.personal_release',
                    'seedtime'         => 'history.seedtime',
                    'connectable'      => 'MAX(peers.connectable)',
                };

                $earningsQuery .= match ($bonEarning->operation) {
                    'append'   => " + CASE WHEN ({$conditionQuery}) THEN {$variable} * {$bonEarning->multiplier} ELSE 0 END)",
                    'multiply' => " * CASE WHEN ({$conditionQuery}) THEN {$variable} * {$bonEarning->multiplier} ELSE 1 END)",
                };
            }

            $query = DB::table('peers')
                ->select([
                    DB::raw('1 as "1"'),
                    'torrents.name',
                    DB::raw('TIMESTAMPDIFF(SECOND, torrents.created_at, NOW()) as age'),
                    'torrents.type_id',
                    'torrents.size',
                    'torrents.seeders',
                    'torrents.leechers',
                    'torrents.times_completed',
                    'history.seedtime',
                    'torrents.personal_release',
                    'torrents.internal',
                    DB::raw('MAX(peers.connectable) as connectable'),
                    'peers.torrent_id',
                    'peers.user_id',
                    DB::raw("({$earningsQuery}) AS hourly_earnings"),
                ])
                ->join('history', fn ($join) => $join->on('history.torrent_id', '=', 'peers.torrent_id')->on('history.user_id', '=', 'peers.user_id'))
                ->join('torrents', 'peers.torrent_id', '=', 'torrents.id')
                ->where('peers.seeder', '=', true)
                ->where('peers.active', '=', true)
                ->where('peers.user_id', '=', $this->user->id)
                ->where('peers.created_at', '<', now()->subMinutes(30))
                ->where('torrents.name', 'LIKE', '%'.str_replace(' ', '%', $this->torrentName).'%')
                ->groupBy(['peers.torrent_id', 'peers.user_id']);

            return $query;
        }
    }

    /**
     * @var \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Peer>
     */
    final protected \Illuminate\Contracts\Pagination\LengthAwarePaginator $torrents {
        get => $this
            ->query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    /**
     * @return float|numeric-string
     */
    final protected float|string $total {
        get => DB::query()->fromSub($this->query, 'earnings_per_torrent')->sum('hourly_earnings');
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user-earnings', [
            'user'        => $this->user,
            'bon'         => $this->user->formatted_seedbonus,
            'total'       => $this->total,
            'torrents'    => $this->torrents,
            'bonEarnings' => $this->bonEarnings,
        ]);
    }
}
