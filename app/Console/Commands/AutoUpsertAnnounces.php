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

use App\Models\Announce;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;
use Throwable;

class AutoUpsertAnnounces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:upsert_announces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upserts announces in batches';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        /**
         * MySql can handle a max of 65535 placeholders per query,
         * and there are 11 fields on each announce that are inserted:
         *
         * - user_id
         * - torrent_id
         * - uploaded
         * - downloaded
         * - left
         * - corrupt
         * - peer_id
         * - port
         * - numwant
         * - event
         * - key
         */
        $announcesPerCycle = intdiv(65_000, 11);

        $key = config('cache.prefix').':announces:batch';
        $announceCount = Redis::connection('announce')->command('LLEN', [$key]);

        for ($announcesLeft = $announceCount; $announcesLeft > 0; $announcesLeft -= $announcesPerCycle) {
            $announces = Redis::connection('announce')->command('LPOP', [$key, $announcesPerCycle]);

            if ($announces === false) {
                break;
            }

            $announces = array_map('unserialize', $announces);

            DB::transaction(static fn () => Announce::query()->insert($announces), 5);
        }

        $this->comment('Automated upsert announce command complete');
    }
}
