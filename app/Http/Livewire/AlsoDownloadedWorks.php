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
use App\Models\IgdbGame;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AlsoDownloadedWorks extends Component
{
    public TmdbMovie|TmdbTv|IgdbGame $work;

    public int $categoryId;

    /**
     * @var Collection<int, TmdbMovie>|Collection<int, TmdbTv>|Collection<int, IgdbGame>
     */
    final protected Collection $alsoDownloadedWorks {
        get => match ($this->work::class) {
            TmdbMovie::class => cache()->flexible(
                'also-downloaded:by-tmdb-movie-id:'.$this->work->id,
                [3600 * 12, 3600 * 24 * 14],
                fn () => TmdbMovie::query()
                    ->withMin('torrents', 'category_id')
                    ->addSelect('total')
                    ->joinSub(
                        Torrent::query()
                            ->select('tmdb_movie_id', DB::raw('COUNT(DISTINCT history.user_id) AS total'))
                            ->join('history', 'torrents.id', '=', 'history.torrent_id')
                            ->whereIn(
                                'history.user_id',
                                History::query()
                                    ->select('user_id')
                                    ->whereIn(
                                        'torrent_id',
                                        Torrent::query()
                                            ->select('id')
                                            ->where('tmdb_movie_id', '=', $this->work->id)
                                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                                    )
                            )
                            ->where('tmdb_movie_id', '!=', $this->work->id)
                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                            ->groupBy('tmdb_movie_id')
                            ->orderByDesc('total')
                            ->limit(30),
                        'also_downloaded',
                        fn ($join) => $join->on('tmdb_movies.id', '=', 'also_downloaded.tmdb_movie_id')
                    )
                    ->orderByDesc('total')
                    ->get()
            ),
            TmdbTv::class => cache()->flexible(
                'also-downloaded:by-tmdb-tv-id:'.$this->work->id,
                [3600 * 12, 3600 * 24 * 14],
                fn () => TmdbTv::query()
                    ->withMin('torrents', 'category_id')
                    ->addSelect('total')
                    ->joinSub(
                        Torrent::query()
                            ->select('tmdb_tv_id', DB::raw('COUNT(DISTINCT history.user_id) AS total'))
                            ->join('history', 'torrents.id', '=', 'history.torrent_id')
                            ->whereIn(
                                'history.user_id',
                                History::query()
                                    ->select('user_id')
                                    ->whereIn(
                                        'torrent_id',
                                        Torrent::query()
                                            ->select('id')
                                            ->where('tmdb_tv_id', '=', $this->work->id)
                                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                                    )
                            )
                            ->where('tmdb_tv_id', '!=', $this->work->id)
                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                            ->groupBy('tmdb_tv_id')
                            ->orderByDesc('total')
                            ->limit(30),
                        'also_downloaded',
                        fn ($join) => $join->on('tmdb_tv.id', '=', 'also_downloaded.tmdb_tv_id')
                    )
                    ->orderByDesc('total')
                    ->get()
            ),
            IgdbGame::class => cache()->flexible(
                'also-downloaded:by-igdb-game-id:'.$this->work->id,
                [3600 * 12, 3600 * 24 * 14],
                fn () => IgdbGame::query()
                    ->withMin('torrents', 'category_id')
                    ->addSelect('total')
                    ->joinSub(
                        Torrent::query()
                            ->select('igdb', DB::raw('COUNT(DISTINCT history.user_id) AS total'))
                            ->join('history', 'torrents.id', '=', 'history.torrent_id')
                            ->whereIn(
                                'history.user_id',
                                History::query()
                                    ->select('user_id')
                                    ->whereIn(
                                        'torrent_id',
                                        Torrent::query()
                                            ->select('id')
                                            ->where('igdb', '=', $this->work->id)
                                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                                    )
                            )
                            ->where('igdb', '!=', $this->work->id)
                            ->whereRaw('history.created_at > torrents.created_at + INTERVAL 30 MINUTE')
                            ->groupBy('igdb')
                            ->orderByDesc('total')
                            ->limit(30),
                        'also_downloaded',
                        fn ($join) => $join->on('igdb_games.id', '=', 'also_downloaded.igdb')
                    )
                    ->orderByDesc('total')
                    ->get()
            ),
        };
    }

    final public function placeholder(): string
    {
        return <<<'HTML'
        <section class="panelV2">
            <h2 class="panel__heading">Also downloaded</h2>
            <div class="panel__body">Loading...</div>
        </section>
        HTML;
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.also-downloaded-works', [
            'alsoDownloadedWorks' => $this->alsoDownloadedWorks,
        ]);
    }
}
