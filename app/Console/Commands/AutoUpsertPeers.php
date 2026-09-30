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

namespace App\Console\Commands;

use App\Models\Peer;
use App\Services\TorrentPeerCountSync;
use App\Traits\FiltersOrphanedAnnounceRows;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;
use Throwable;

class AutoUpsertPeers extends Command
{
    use FiltersOrphanedAnnounceRows;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:upsert_peers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upserts peers in batches';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(TorrentPeerCountSync $torrentPeerCountSync): void
    {
        $announcedTorrentIds = [];

        /**
         * MySql can handle a max of 65k placeholders per query,
         * and there are 15 fields on each peer that are updated.
         * (`active`, `agent`, `connectable`, `created_at`, `downloaded`, `id`, `ip`, `left`, `peer_id`, `port`, `seeder`, `torrent_id`, `updated_at`, `uploaded`, `visible`, `user_id`).
         */
        $peerPerCycle = intdiv(65_000, 16);

        $key = config('cache.prefix').':peers:batch';
        $peerCount = Redis::connection('announce')->command('LLEN', [$key]);

        for ($peersLeft = $peerCount; $peersLeft > 0; $peersLeft -= $peerPerCycle) {
            $peers = Redis::connection('announce')->command('LRANGE', [$key, 0, $peerPerCycle - 1]);

            if ($peers === false) {
                break;
            }

            $peers = $this->withoutOrphanedRows(array_map('unserialize', $peers));

            if ($peers === []) {
                Redis::connection('announce')->command('LTRIM', [$key, $peerPerCycle, -1]);

                continue;
            }

            DB::transaction(function () use ($peers): void {
                Peer::upsert(
                    $peers,
                    ['user_id', 'torrent_id', 'peer_id'],
                    [
                        'peer_id',
                        'ip',
                        'port',
                        'agent',
                        'uploaded',
                        'downloaded',
                        'left',
                        'seeder',
                        'torrent_id',
                        'user_id',
                        'connectable',
                        'active',
                        'visible',
                    ],
                );
            }, 5);

            Redis::connection('announce')->command('LTRIM', [$key, $peerPerCycle, -1]);

            foreach ($peers as $peer) {
                $announcedTorrentIds[(int) $peer['torrent_id']] = true;
            }
        }

        // Refresh counters and the search index for the torrents that just announced.
        $torrentPeerCountSync->sync(array_keys($announcedTorrentIds));

        $this->comment('Automated insert peers command complete');
    }
}
