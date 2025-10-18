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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Exception;
use Throwable;

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
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
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
            $histories = Redis::connection('announce')->command('LRANGE', [$key, 0, $historiesPerCycle - 1]);

            if ($histories === false) {
                break;
            }

            $histories = array_map('unserialize', $histories);

            DB::transaction(function () use ($histories): void {
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
                        // 5400 is the max announce interval defined in the announce controller
                        // We need to make sure seeder and active are updated after seedtime, otherwise the seedtime logic for ensuring it's not a new announce and the left was 0 in the last announce breaks.
                        // Unfortunately, laravel sorts the keys in this array alphabetically when inserting so reordering the keys themselves in this array doesn't work.
                        // This leaves us with this hacky fix.
                        'seedtime'     => DB::raw('CASE WHEN updated_at + INTERVAL 5400 SECOND > VALUES(updated_at) AND seeder = TRUE AND active = TRUE AND VALUES(seeder) = TRUE THEN seedtime + TIMESTAMPDIFF(SECOND, updated_at, VALUES(updated_at)) ELSE seedtime END, seeder = VALUES(seeder), active = VALUES(active)'),
                        'immune'       => DB::raw('immune AND VALUES(immune)'),
                        'completed_at' => DB::raw('COALESCE(completed_at, VALUES(completed_at))'),
                    ],
                );
            }, 5);

            Redis::connection('announce')->command('LTRIM', [$key, $historiesPerCycle, -1]);
        }

        $this->comment('Automated upsert histories command complete');
    }
}
