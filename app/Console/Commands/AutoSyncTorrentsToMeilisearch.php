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

use App\Models\Torrent;
use Exception;
use Illuminate\Console\Command;

class AutoSyncTorrentsToMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:sync_torrents_to_meilisearch {--wipe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs torrents and their relations to meilisearch';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $start = now();

        if ($this->option('wipe')) {
            Torrent::removeAllFromSearch();
            Torrent::query()->selectRaw(Torrent::SEARCHABLE)->searchable();
        } else {
            // Reindex torrents that were updated since the start of the most
            // recently finished 15-minute period (since this cron job runs
            // every 15 minutes, but might be delayed by a few seconds/minutes
            // each time)
            $since = now()->startOfHour()->addMinutes(15 * (intdiv((int) now()->diffInMinutes(now()->startOfHour(), true), 15) - 1));

            Torrent::query()
                ->selectRaw(Torrent::SEARCHABLE)
                ->where('updated_at', '>', $since)
                ->searchable();
        }

        $this->comment('Synced all torrents to Meilisearch in '.((int) now()->diffInMilliseconds($start, true) / 1000).' seconds.');
    }
}
