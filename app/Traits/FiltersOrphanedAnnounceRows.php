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

namespace App\Traits;

use App\Models\Torrent;
use App\Models\User;

trait FiltersOrphanedAnnounceRows
{
    /**
     * Drop buffered announce rows whose user or torrent no longer exists.
     *
     * Announces are buffered in redis and flushed later, so a user or torrent
     * deleted in between leaves rows that violate the foreign keys of `peers`,
     * `histories` and `announces`. Since the whole batch is written in a single
     * statement, one such row aborts every flush and stalls all peer bookkeeping
     * until it is removed by hand.
     *
     * @param  array<int, array{user_id: int, torrent_id: int, ...}> $rows
     * @return array<int, array{user_id: int, torrent_id: int, ...}>
     */
    private function withoutOrphanedRows(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $userIds = User::query()
            ->whereIntegerInRaw('id', array_unique(array_column($rows, 'user_id')))
            ->pluck('id')
            ->flip();

        $torrentIds = Torrent::query()
            ->withoutGlobalScopes()
            ->whereIntegerInRaw('id', array_unique(array_column($rows, 'torrent_id')))
            ->pluck('id')
            ->flip();

        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => $userIds->has($row['user_id']) && $torrentIds->has($row['torrent_id']),
        ));
    }
}
