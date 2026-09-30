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

namespace App\Console\Commands;

use App\Models\History;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Services\TorrentPeerCountSync;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncPeers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:sync_peers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync torrent seeders/leechers/times completed count.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(TorrentPeerCountSync $torrentPeerCountSync): void
    {
        // Full pass; announce batches already sync their own torrents (see AutoUpsertPeers),
        // this catches peers that expired without announcing.
        $torrentPeerCountSync->sync();

        DB::transaction(function (): void {
            Torrent::withoutGlobalScope(ApprovedScope::class)
                ->leftJoinSub(
                    History::query()
                        ->select('torrent_id')
                        ->addSelect(DB::raw('SUM(completed_at IS NOT NULL) as updated_times_completed'))
                        ->groupBy('torrent_id'),
                    'all_times_completed',
                    fn ($join) => $join->on('torrents.id', '=', 'all_times_completed.torrent_id'),
                )
                ->where('times_completed', '!=', DB::raw('COALESCE(updated_times_completed, 0)'))
                ->update([
                    'times_completed' => DB::raw('COALESCE(updated_times_completed, 0)'),
                ]);
        }, 5);

        $this->info('Torrent seeders/leechers/times completed count synced successfully!');
    }
}
