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

use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class SyncTorrentSeasonEpisode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:sync_torrent_season_episode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs season and episode numbers from torrent titles to database';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        foreach (Torrent::withoutGlobalScope(ApprovedScope::class)->with(['category'])->whereNull('season_number')->orWhereNull('episode_number')->get() as $torrent) {
            // Skip if not TV
            if (!$torrent->category->tv_meta) {
                continue;
            }

            if (preg_match('~\.{0,1}S(?<season>\d+)\.{0,1}( ){0,1}E(?<episode>\d+)~', (string) $torrent->name, $match)) {
                // Match SxxExx, Sxx.Exx, Sxx Exx (Single Episodes)
                $torrent->update([
                    'season_number'  => (int) $match['season'],
                    'episode_number' => (int) $match['episode'],
                ]);
            } elseif (preg_match('~\.{0,1}S(?<season>\d+)\.{0,1}( )~', (string) $torrent->name, $match)) {
                // Match Sxx (Complete Seasons)
                $torrent->update([
                    'season_number'  => (int) $match['season'],
                    'episode_number' => 0,
                ]);
            } elseif (preg_match('~\.{0,1}( ){0,1}E(?<episode>\d+)~', (string) $torrent->name, $match)) {
                // Match Exx (Single Episode without Season Number)
                $torrent->update([
                    'season_number'  => 1,
                    'episode_number' => (int) $match['episode'],
                ]);
            }
        }

        $this->comment('Torrent season episode sync command complete');
    }
}
