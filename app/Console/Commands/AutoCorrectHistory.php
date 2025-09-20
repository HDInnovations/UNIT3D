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
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoCorrectHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:correct_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrects history records said to be active even though really are not due to not receiving a stopped event from client.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        History::query()
            ->where('active', '=', 1)
            ->where('updated_at', '<', Carbon::now()->subHours(2))
            ->update([
                'active'     => 0,
                'updated_at' => DB::raw('updated_at'),
            ]);

        $this->comment('Automated history record correction command complete');
    }
}
