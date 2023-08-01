<?php
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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;

class AutoUpsertHistories extends Command
{
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
     * @throws Exception
     */
    public function handle(): void
    {
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

        $key = config('cache.prefix').':histories:batch';
        $historyCount = Redis::connection('announce')->command('LLEN', [$key]);

        for ($historiesLeft = $historyCount; $historiesLeft > 0; $historiesLeft -= $historiesPerCycle) {
            $histories = Redis::connection('announce')->command('LPOP', [$key, $historiesPerCycle]);
            $histories = array_map('unserialize', $histories);

            History::upsert(
                $histories,
                ['user_id', 'torrent_id'],
                [
                    'agent',
                    'uploaded'        => DB::raw('uploaded + VALUES(uploaded)'),
                    'actual_uploaded' => DB::raw('actual_uploaded + VALUES(actual_uploaded)'),
                    'client_uploaded',
                    'downloaded'        => DB::raw('downloaded + VALUES(downloaded)'),
                    'actual_downloaded' => DB::raw('actual_downloaded + VALUES(actual_downloaded)'),
                    'client_downloaded',
                    'seeder',
                    'active',
                    // 5400 is the max announce interval defined in the announce controller
                    'seedtime'     => DB::raw('IF(DATE_ADD(updated_at, INTERVAL 5400 SECOND) > VALUES(updated_at) AND seeder = 1 AND VALUES(seeder) = 1, seedtime + TIMESTAMPDIFF(SECOND, updated_at, VALUES(updated_at)), seedtime)'),
                    'immune'       => DB::raw('immune AND VALUES(immune)'),
                    'completed_at' => DB::raw('COALESCE(completed_at, VALUES(completed_at))'),
                ],
            );
        }

        $this->comment('Automated upsert histories command complete');
    }
}
