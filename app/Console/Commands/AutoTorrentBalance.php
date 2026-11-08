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
use App\Models\Torrent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoTorrentBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:torrent_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate balance for all torrents.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        DB::transaction(static function (): void {
            Torrent::query()->withoutGlobalScopes()->joinSub(
                History::query()
                    ->withTrashed()
                    ->select('torrent_id')
                    ->selectRaw('SUM(actual_uploaded) - SUM(actual_downloaded) AS balance')
                    ->groupBy('torrent_id'),
                'balances',
                static fn ($join) => $join->on('balances.torrent_id', '=', 'torrents.id')
            )
                ->update([
                    'torrents.balance' => DB::raw('balances.balance'),
                    'updated_at'       => DB::raw('updated_at'),
                ]);
        }, 5);

        $this->comment('Torrent balance calculations completed.');
    }
}
