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

use App\Models\Audit;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class AutoRecycleAudits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:recycle_audits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle audits once X days old.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        Audit::query()
            ->where('created_at', '<', now()->subDays(config('audit.recycle'))->max(Carbon::createFromTimestamp(0)))
            ->delete();

        $this->comment('Automated audit recycle command complete');
    }
}
