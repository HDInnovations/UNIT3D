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

use App\Models\TorrentReseed;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class AutoRemoveReseeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:remove_reseeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically remove reseeds that are no longer being leeched or have sufficient seeders';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        TorrentReseed::query()
            ->whereDoesntHave(
                'history',
                fn ($query) => $query
                    ->where('seeder', '=', false)
                    ->where('updated_at', '>', now()->subDays(14))
            )
            ->orWhereHas(
                'torrent',
                fn ($query) => $query
                    ->where('seeders', '>=', 4)
                    ->where('leechers', '=', 0)
            )
            ->delete();

        $this->comment('Automated remove reseeds command complete');
    }
}
