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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Torrent;
use Illuminate\Support\Facades\DB;

class CheatedTorrentController extends Controller
{
    /**
     * Cheated Torrents.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.cheated-torrent.index', [
            'torrents' => Torrent::query()
                ->select([
                    'torrents.id',
                    'torrents.name',
                    'torrents.seeders',
                    'torrents.leechers',
                    'torrents.times_completed',
                    'torrents.size',
                    'torrents.balance',
                    'torrents.balance_offset',
                    'torrents.balance_reset_at',
                    'torrents.created_at',
                ])
                ->selectRaw('balance + balance_offset AS current_balance')
                ->selectRaw('(balance + balance_offset) / GREATEST(size, 1) AS times_cheated')
                // Exclude torrents that have active data transfer when the current balance (or its offset) is being calculated.
                // The balance is inaccurate during these times since seeds only report upload data once per announce interval (max 1 hour).
                // Balances are only accurate once it's been at least 1 announce interval since the last leech completed the torrent.
                ->whereDoesntHave(
                    'history',
                    fn ($query) => $query
                        // Exclude torrents where the reporting period overlapped with the balance reset, but eventually completed
                        ->where(
                            fn ($query) => $query
                                ->whereNotNull('balance_reset_at')
                                ->whereColumn('balance_reset_at', '>', 'history.created_at')
                                ->whereColumn(DB::raw('balance_reset_at - INTERVAL 1 HOUR'), '<', 'history.completed_at')
                                ->whereNotNull('completed_at')
                        )
                        // Exclude torrents where the reporting period overlapped with both the balance reset and the current balance calculation
                        ->orWhere(
                            fn ($query) => $query
                                ->whereNotNull('balance_reset_at')
                                ->whereColumn('balance_reset_at', '>', 'history.created_at')
                                ->whereNull('completed_at')
                                ->where('active', '=', true)
                                ->where('seeder', '=', false)
                        )
                        // Exclude torrents where the reporting period overlapped with right now
                        ->orWhereColumn(DB::raw('NOW() - INTERVAL 1 HOUR'), '<', 'created_at')
                        ->orWhereColumn(DB::raw('NOW() - INTERVAL 1 HOUR'), '<', 'completed_at')
                )
                // Tolerance of 5%
                // @phpstan-ignore argument.type (This function works with DB::raw() even though larastan doesn't think so)
                ->havingBetween('current_balance', [DB::raw('-0.05 * size'), DB::raw('0.05 * size')], 'and', true)
                ->orderByDesc('times_cheated')
                ->paginate(25),
        ]);
    }

    /**
     * Reset the balance of a cheated torrent.
     */
    public function destroy(Torrent $cheatedTorrent): \Illuminate\Http\RedirectResponse
    {
        $cheatedTorrent->update([
            'balance_offset'   => DB::raw('balance * -1'),
            'balance_reset_at' => now(),
        ]);

        return to_route('staff.cheated_torrents.index')
            ->with('success', 'Balance successfully reset');
    }

    /**
     * Reset the balance of a cheated torrent.
     */
    public function massDestroy(): \Illuminate\Http\RedirectResponse
    {
        Torrent::query()->update([
            'balance_offset'   => DB::raw('balance * -1'),
            'balance_reset_at' => now(),
        ]);

        return to_route('staff.cheated_torrents.index')
            ->with('success', 'All balances successfully reset');
    }
}
