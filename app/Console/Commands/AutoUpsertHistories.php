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

use App\Models\History;
use App\Traits\FiltersOrphanedAnnounceRows;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;
use Throwable;

class AutoUpsertHistories extends Command
{
    use FiltersOrphanedAnnounceRows;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:upsert_histories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upserts peer histories in batches';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $this->pruneJobReceipts();

        $this->withFlushSingleFlight('histories', function (callable $stillHoldsLock): void {
            /**
             * MySql can handle a max of 65535 placeholders per query,
             * and there are 16 fields on each history that are updated:.
             *
             * - user_id
             * - torrent_id
             * - agent
             * - uploaded
             * - actual_uploaded
             * - client_uploaded
             * - downloaded
             * - actual_downloaded
             * - client_downloaded
             * - seeder
             * - active
             * - seedtime
             * - immune
             * - completed_at
             * - created_at
             * - updated_at
             */
            $historiesPerCycle = intdiv(65_000, 16);

            $seedtimeGraceSeconds = max(5_400, intdiv(3 * (int) config('announce.interval.max'), 2));

            $key = config('cache.prefix').':histories:batch';
            $historyCount = Redis::connection('announce')->command('LLEN', [$key]);

            for ($historiesLeft = $historyCount; $historiesLeft > 0; $historiesLeft -= $historiesPerCycle) {
                if (!$stillHoldsLock()) {
                    break;
                }

                $rawHistories = Redis::connection('announce')->command('LRANGE', [$key, 0, $historiesPerCycle - 1]);

                if ($rawHistories === false || $rawHistories === []) {
                    break;
                }

                // Trim exactly what was just read, never the fixed per-cycle
                // limit -- see AutoUpsertPeers for why a fixed-size trim can
                // delete rows a producer appended after this LRANGE.
                $actualCount = \count($rawHistories);

                $histories = $this->withoutOrphanedRows(array_map('unserialize', $rawHistories));

                if ($histories === []) {
                    Redis::connection('announce')->command('LTRIM', [$key, $actualCount, -1]);

                    continue;
                }

                $jobUuids = array_column($histories, '_job_uuid');
                $newHistories = $this->withoutDuplicateJobs('histories', $histories);

                if ($newHistories !== []) {
                    DB::transaction(function () use ($newHistories, $seedtimeGraceSeconds): void {
                        $rows = $this->recordJobReceiptsAndStripKey('histories', $newHistories);

                        History::upsert(
                            $rows,
                            ['user_id', 'torrent_id'],
                            [
                                'agent',
                                'uploaded'        => DB::raw('uploaded + VALUES(uploaded)'),
                                'actual_uploaded' => DB::raw('actual_uploaded + VALUES(actual_uploaded)'),
                                'client_uploaded',
                                'downloaded'        => DB::raw('downloaded + VALUES(downloaded)'),
                                'actual_downloaded' => DB::raw('actual_downloaded + VALUES(actual_downloaded)'),
                                'client_downloaded',
                                // Seedtime accrues only when consecutive announces are closer than the grace window:
                                // 1.5x the configured max announce interval, never below 5400 s so clients that ignore
                                // a short tracker interval (fixed 30-60 min announces) keep accruing seedtime.
                                // We need to make sure seeder and active are updated after seedtime, otherwise the seedtime logic for ensuring it's not a new announce and the left was 0 in the last announce breaks.
                                // Unfortunately, laravel sorts the keys in this array alphabetically when inserting so reordering the keys themselves in this array doesn't work.
                                // This leaves us with this hacky fix.
                                'seedtime'     => DB::raw('CASE WHEN updated_at + INTERVAL '.$seedtimeGraceSeconds.' SECOND > VALUES(updated_at) AND seeder = TRUE AND active = TRUE AND VALUES(seeder) = TRUE THEN seedtime + TIMESTAMPDIFF(SECOND, updated_at, VALUES(updated_at)) ELSE seedtime END, seeder = VALUES(seeder), active = VALUES(active)'),
                                'immune'       => DB::raw('immune AND VALUES(immune)'),
                                'completed_at' => DB::raw('COALESCE(completed_at, VALUES(completed_at))'),
                            ],
                        );
                    }, 5);
                }

                if (!$stillHoldsLock()) {
                    break;
                }

                Redis::connection('announce')->command('LTRIM', [$key, $actualCount, -1]);

                $this->acknowledgeJobReceipts('histories', $jobUuids);
            }
        });

        $this->comment('Automated upsert histories command complete');
    }
}
