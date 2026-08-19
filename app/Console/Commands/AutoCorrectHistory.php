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
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('auto:correct_history')]
#[Description('Corrects history records said to be active even though really are not due to not receiving a stopped event from client.')]
class AutoCorrectHistory extends Command
{
    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        History::query()
            ->withTrashed()
            ->where('active', '=', 1)
            ->where('updated_at', '<', now()->subHours(2))
            ->update([
                'active'     => 0,
                'updated_at' => DB::raw('updated_at'),
            ]);

        $this->comment('Automated history record correction command complete');
    }
}
