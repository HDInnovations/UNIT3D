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

use App\Models\Torrent;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoRemoveTimedTorrentBuffs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:remove_torrent_buffs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically removes torrent buffs if expired';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $flTorrents = Torrent::whereNotNull('fl_until')->where('fl_until', '<', Carbon::now())->get();

        foreach ($flTorrents as $torrent) {
            $torrent->update([
                'free'     => 0,
                'fl_until' => null,
            ]);

            cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);
            Unit3dAnnounce::addTorrent($torrent);
        }

        $duTorrents = Torrent::whereNotNull('du_until')->where('du_until', '<', Carbon::now())->get();

        foreach ($duTorrents as $torrent) {
            $torrent->update([
                'doubleup' => false,
                'du_until' => null,
            ]);

            cache()->forget('announce-torrents:by-infohash:'.$torrent->info_hash);
            Unit3dAnnounce::addTorrent($torrent);
        }

        Torrent::query()
            ->whereNotNull('bumped_at')
            ->where('bumped_at', '<', now()->subWeek())
            ->whereColumn('bumped_at', '!=', 'created_at')
            ->update([
                'bumped_at' => DB::raw('created_at'),
            ]);

        $this->comment('Automated removal of expired torrent buffs command complete');
    }
}
