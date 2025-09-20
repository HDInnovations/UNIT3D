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

use App\Models\Peer;
use App\Models\Scopes\ApprovedScope;
use App\Models\Seedbox;
use App\Models\Torrent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoHighspeedTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:highspeed_tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates torrents highspeed tag based on registered seedboxes.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $seedboxIps = Seedbox::all()
            ->pluck('ip')
            ->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP) !== false)
            ->map(fn ($ip) => inet_pton($ip));

        Torrent::withoutGlobalScope(ApprovedScope::class)
            ->leftJoinSub(
                Peer::where('seeder', '=', 1)
                    ->where('active', '=', 1)
                    ->distinct()
                    ->select('torrent_id')
                    ->whereIn('ip', $seedboxIps),
                'highspeed_torrents',
                fn ($join) => $join->on('torrents.id', '=', 'highspeed_torrents.torrent_id')
            )
            ->update([
                'highspeed'  => DB::raw('CASE WHEN highspeed_torrents.torrent_id IS NOT NULL THEN TRUE ELSE FALSE END'),
                'updated_at' => DB::raw('updated_at'),
            ]);

        $this->comment('Automated high speed torrents command complete');
    }
}
