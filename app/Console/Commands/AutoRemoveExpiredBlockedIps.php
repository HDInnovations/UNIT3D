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

use App\Models\BlockedIp;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class AutoRemoveExpiredBlockedIps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:remove_expired_blocked_ips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically remove expired blocked IPs.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $start = now();

        BlockedIp::query()->where('expires_at', '<', now())->delete();

        cache()->forget('blocked-ips');

        $this->info('Removed expired blocked ips in '.now()->diffInSeconds($start).' s.');
    }
}
