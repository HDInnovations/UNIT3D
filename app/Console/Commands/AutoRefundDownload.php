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

use App\Models\History;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoRefundDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:refund_download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refunds download to users based on seed time.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $now = Carbon::now();
        $MIN_SEEDTIME = config('hitrun.seedtime');
        $FULL_REFUND_SEEDTIME = 12 * 30 * 24 * 60 * 60 + $MIN_SEEDTIME;
        $COMMAND_RUN_PERIOD = 24 * 60 * 60; // This command is run every 24 hours

        History::query()
            ->selectRaw('LEAST(1, history.seedtime / ?) * torrents.size - history.refunded_download as refunded_download_delta', [$FULL_REFUND_SEEDTIME])
            ->join('torrents', 'torrents.id', '=', 'history.torrent_id')
            ->join('users', 'users.id', '=', 'history.user_id')
            ->join('groups', 'groups.id', '=', 'users.group_id')
            ->where('history.active', '=', 1)
            ->where('history.seeder', '=', 1)
            ->where('history.seedtime', '>=', $MIN_SEEDTIME)
            ->where('history.seedtime', '<=', $FULL_REFUND_SEEDTIME + $MIN_SEEDTIME + $COMMAND_RUN_PERIOD)
            ->where('history.created_at', '<=', $now->copy()->subSeconds($MIN_SEEDTIME))
            ->whereColumn('torrents.user_id', '!=', 'history.user_id')
            ->when(!config('other.refundable'), fn ($query) => $query->where(
                fn ($query) => $query
                    ->where('groups.is_refundable', '=', true)
                    ->orWhere('torrents.refundable', '=', true)
            ))
            ->update([
                'history.refunded_download' => DB::raw('history.refunded_download + (@delta := LEAST(1, history.seedtime / '.(int) $FULL_REFUND_SEEDTIME.') * torrents.size - history.refunded_download)'),
                'users.downloaded'          => DB::raw('GREATEST(0, users.downloaded - @delta)'),
                'history.updated_at'        => DB::raw('history.updated_at'),
            ]);

        $this->comment('Automated download refund command complete');
    }
}
