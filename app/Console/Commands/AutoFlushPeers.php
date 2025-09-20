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
use App\Models\Peer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoFlushPeers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:flush_peers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flushes ghost peers';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $carbon = new Carbon();
        $peers = Peer::select(['torrent_id', 'user_id', 'peer_id', 'seeder', 'updated_at'])
            ->where('updated_at', '<', $carbon->copy()->subHours(2))
            ->where('active', '=', 1)
            ->get();

        foreach ($peers as $peer) {
            History::query()
                ->where('torrent_id', '=', $peer->torrent_id)
                ->where('user_id', '=', $peer->user_id)
                ->update([
                    'active'     => false,
                    'updated_at' => DB::raw('updated_at')
                ]);

            Peer::query()
                ->where('torrent_id', '=', $peer->torrent_id)
                ->where('user_id', '=', $peer->user_id)
                ->where('peer_id', '=', $peer->peer_id)
                ->update([
                    'active'     => false,
                    'updated_at' => DB::raw('updated_at'),
                ]);
        }

        // Keep peers that stopped being announced without a `stopped` event
        // in case a user has internet issues and comes back online within the
        // next 2 days
        if (config('announce.external_tracker.is_enabled')) {
            Peer::query()
                ->where('updated_at', '<', $carbon->copy()->subDays(2))
                ->where('active', '=', 0)
                ->delete();
        } else {
            $peers = Peer::select(['torrent_id', 'user_id', 'peer_id'])
                ->where('updated_at', '<', $carbon->copy()->subDays(2))
                ->where('active', '=', 0)
                ->get();

            foreach ($peers as $peer) {
                cache()->decrement('user-leeching-count:'.$peer->user_id);

                Peer::query()
                    ->where('torrent_id', '=', $peer->torrent_id)
                    ->where('user_id', '=', $peer->user_id)
                    ->where('peer_id', '=', $peer->peer_id)
                    ->delete();
            }
        }

        $this->comment('Automated flush ghost peers command complete');
    }
}
