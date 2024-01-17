<?php
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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * @see \Tests\Unit\Console\Commands\AutoHighspeedTagTest
 */
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
    protected $description = 'Updates Torrents Highspeed Tag Based On Registered Seedboxes.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $seedboxIps = Seedbox::all()
            ->pluck('ip')
            ->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP));

        $peers = Peer::where('seeder', '=', 1)
            ->where('active', '=', 1)
            ->distinct()
            ->select('torrent_id')
            ->whereIn(DB::raw("INET6_NTOA(ip)"), $seedboxIps)
            ->get();

        $torrents = Torrent::withoutGlobalScope(ApprovedScope::class)
            ->get();

        foreach ($torrents as $torrent) {
            $torrent->highspeed = $peers->contains('torrent_id', $torrent->id) ? 1 : 0;
            $torrent->save();
        }

        $this->comment('Automated High Speed Torrents Command Complete');
    }
}
