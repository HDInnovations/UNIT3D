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

use App\Models\Invite;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class AutoRecycleInvites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:recycle_invites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle invites that are expired.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        Invite::query()
            ->whereNull('accepted_by')
            ->whereNull('accepted_at')
            ->where('expires_on', '<', now())
            ->delete();

        $this->comment('Automated purge unaccepted invites command complete');
    }
}
