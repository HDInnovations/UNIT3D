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

use App\Models\Group;
use App\Models\Warning;
use App\Notifications\UserWarningExpired;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutoDeactivateWarning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:deactivate_warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivates user warnings if expired';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $usersWithExpiredWarnings = [];

        Warning::query()
            ->where('active', '=', true)
            ->where(
                fn ($query) => $query
                    ->where('expires_on', '<=', now())
                    ->orWhereHas(
                        'torrent.history',
                        fn ($query) => $query
                            ->whereColumn('history.user_id', '=', 'warnings.user_id')
                            ->where('history.seedtime', '>=', config('hitrun.seedtime'))
                    )
            )
            ->each(function ($warning) use (&$usersWithExpiredWarnings): void {
                // Set Records Active To 0 in warnings table
                $warning->update(['active' => false]);

                // Add user to usersWithExpiredWarnings array
                $usersWithExpiredWarnings[$warning->user_id] = $warning->user;
            }, 100);

        $inactiveGroupIds = Group::query()
            ->whereIn('slug', ['banned', 'validating', 'disabled', 'pruned'])
            ->pluck('id')
            ->all();

        // Send a single notification for each user with expired warnings
        foreach ($usersWithExpiredWarnings as $user) {
            if (\in_array($user->group_id, $inactiveGroupIds, true)) {
                continue;
            }

            $user->notify(new UserWarningExpired($user));
        }

        // Calculate User Warning Count and Enable DL Priv If Required.
        Warning::query()
            ->with('user')
            ->select(DB::raw('user_id, SUM(active = TRUE) as value'))
            ->groupBy('user_id')
            ->having('value', '<', config('hitrun.max_warnings'))
            ->whereRelation('user', 'can_download', '=', false)
            ->eachById(function ($warning): void {
                $warning->user->update(['can_download' => 1]);

                cache()->forget('user:'.$warning->user->passkey);

                Unit3dAnnounce::addUser($warning->user);
            }, 100, 'user_id');

        $this->comment('Automated warning deactivation command complete');
    }
}
