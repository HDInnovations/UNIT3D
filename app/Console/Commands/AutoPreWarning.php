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
use App\Notifications\UserPreWarning;
use Illuminate\Console\Command;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoPreWarning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:prewarning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sends pre warning notifications to users';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        if (config('hitrun.enabled') !== true) {
            return;
        }

        $prewarn = History::with(['user'])
            ->whereNull('prewarned_at')
            ->where('hitrun', '=', 0)
            ->where('immune', '=', 0)
            ->where('actual_downloaded', '>', 0)
            ->where('active', '=', 0)
            ->where('seedtime', '<=', config('hitrun.seedtime'))
            ->where('updated_at', '<', now()->subDays(config('hitrun.prewarn')))
            ->has('torrent')
            ->whereRelation('user.group', 'is_immune', '=', false)
            ->whereRelation('user', 'is_donor', '=', false)
            ->whereHas('torrent', fn ($query) => $query->whereRaw('history.actual_downloaded > torrents.size * ?', [config('hitrun.buffer') / 100]))
            ->whereDoesntHave('user.warnings', fn ($query) => $query->withTrashed()->whereColumn('warnings.torrent', '=', 'history.torrent_id'))
            ->get();

        $usersWithPreWarnings = [];

        foreach ($prewarn as $pre) {
            History::query()
                ->where('torrent_id', '=', $pre->torrent_id)
                ->where('user_id', '=', $pre->user_id)
                ->update([
                    'prewarned_at' => now(),
                    'updated_at'   => DB::raw('updated_at'),
                ]);

            // Add user to usersWithWarnings array
            $usersWithPreWarnings[$pre->user_id] = $pre->user;
        }

        // Send a single notification for each user with warnings
        foreach ($usersWithPreWarnings as $user) {
            $user->notify(new UserPreWarning($user));
        }

        $this->comment('Automated user pre-warning command complete');
    }
}
