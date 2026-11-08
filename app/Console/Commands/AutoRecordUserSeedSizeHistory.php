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
use App\Models\UserSeedSizeHistory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoRecordUserSeedSizeHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:record_user_seed_size_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Records current user seed sizes.';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $start = now();

        UserSeedSizeHistory::query()->where('created_at', '<', now()->subDays(15))->delete();

        UserSeedSizeHistory::query()->insertUsing(
            ['user_id', 'seed_size'],
            History::query()
                ->select([
                    'history.user_id',
                    DB::raw('SUM(torrents.size)')
                ])
                ->join('torrents', 'torrents.id', '=', 'history.torrent_id')
                ->whereDoesntHave('user.group', fn ($query) => $query->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned']))
                ->where('history.active', '=', true)
                ->where('history.seeder', '=', true)
                ->groupBy('user_id'),
        );

        $this->comment('Automated record user seed size history command complete in '.$start->diffInSeconds(now()).'s.');
    }
}
