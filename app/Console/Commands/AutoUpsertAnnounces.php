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
use App\Traits\FiltersOrphanedAnnounceRows;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;
use Throwable;

class AutoUpsertAnnounces extends Command
{
    use FiltersOrphanedAnnounceRows;

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
        $this->pruneJobReceipts();

        $this->withFlushSingleFlight('announces', function (callable $stillHoldsLock): void {
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
                if (!$stillHoldsLock()) {
                    break;
                }

                // LRANGE (peek) + LTRIM only after a confirmed DB commit, not
                // LPOP (destructive pop before the write is confirmed): popping
                // first would permanently lose this chunk if the insert below
                // ever fails or the process crashes before it commits.
                $rawAnnounces = Redis::connection('announce')->command('LRANGE', [$key, 0, $announcesPerCycle - 1]);

                if ($rawAnnounces === false || $rawAnnounces === []) {
                    break;
                }

                // Trim exactly what was just read, never the fixed per-cycle
                // limit -- see AutoUpsertPeers for why a fixed-size trim can
                // delete rows a producer appended after this LRANGE.
                $actualCount = \count($rawAnnounces);

                $announces = $this->withoutOrphanedRows(array_map('unserialize', $rawAnnounces));

                if ($announces === []) {
                    Redis::connection('announce')->command('LTRIM', [$key, $actualCount, -1]);

                    continue;
                }

                $jobUuids = array_column($announces, '_job_uuid');
                $newAnnounces = $this->withoutDuplicateJobs('announces', $announces);

                if ($newAnnounces !== []) {
                    DB::transaction(function () use ($newAnnounces): void {
                        $rows = $this->recordJobReceiptsAndStripKey('announces', $newAnnounces);

                        Announce::insert($rows);
                    }, 5);
                }

                if (!$stillHoldsLock()) {
                    break;
                }

                Redis::connection('announce')->command('LTRIM', [$key, $actualCount, -1]);

                $this->acknowledgeJobReceipts('announces', $jobUuids);
            }
        });

        $this->comment('Automated upsert announce command complete');
    }
}
