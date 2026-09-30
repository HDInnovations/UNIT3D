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

namespace App\Services;

use App\Models\Peer;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Recomputes the cached `torrents.seeders` / `torrents.leechers` counters from
 * active, visible peers and pushes changed torrents to the search index, so the
 * torrent list, filters and sorting follow announces within one peer flush.
 */
final class TorrentPeerCountSync
{
    /**
     * @param  list<int>|null $torrentIds Torrents to recompute; `null` recomputes every torrent.
     * @return list<int>      Ids of torrents whose counters changed.
     *
     * @throws Throwable
     */
    public function sync(?array $torrentIds = null): array
    {
        if ($torrentIds === []) {
            return [];
        }

        $changed = DB::transaction(function () use ($torrentIds): array {
            $changed = $this->outdated($torrentIds)->pluck('torrents.id')->map(fn ($id): int => (int) $id)->all();

            if ($changed === []) {
                return [];
            }

            $this->outdated($changed)->update([
                'seeders'  => DB::raw('COALESCE(seeders_leechers.updated_seeders, 0)'),
                'leechers' => DB::raw('COALESCE(seeders_leechers.updated_leechers, 0)'),
            ]);

            return $changed;
        }, 5);

        if ($changed !== []) {
            // Pending/rejected torrents are not indexed, so keep the approved scope here.
            Torrent::query()->selectRaw(Torrent::SEARCHABLE)->whereIntegerInRaw('torrents.id', $changed)->searchable();
        }

        return $changed;
    }

    /**
     * Torrents whose stored counters differ from their live peer counts.
     *
     * @param  list<int>|null   $torrentIds
     * @return Builder<Torrent>
     */
    private function outdated(?array $torrentIds): Builder
    {
        return Torrent::withoutGlobalScope(ApprovedScope::class)
            ->leftJoinSub(
                Peer::query()
                    ->select('torrent_id')
                    ->addSelect(DB::raw('SUM(peers.left = 0 AND peers.active = TRUE AND peers.visible = TRUE) AS updated_seeders'))
                    ->addSelect(DB::raw('SUM(peers.left != 0 AND peers.active = TRUE AND peers.visible = TRUE) AS updated_leechers'))
                    ->when($torrentIds !== null, fn ($query) => $query->whereIntegerInRaw('torrent_id', $torrentIds))
                    ->groupBy('torrent_id'),
                'seeders_leechers',
                fn ($join) => $join->on('torrents.id', '=', 'seeders_leechers.torrent_id')
            )
            ->when($torrentIds !== null, fn ($query) => $query->whereIntegerInRaw('torrents.id', $torrentIds))
            ->where(
                fn ($query) => $query
                    ->where('seeders', '!=', DB::raw('COALESCE(updated_seeders, 0)'))
                    ->orWhere('leechers', '!=', DB::raw('COALESCE(updated_leechers, 0)'))
            );
    }
}
