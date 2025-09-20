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

use App\Jobs\SendDisableUserMail;
use App\Models\Group;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Exception;
use Throwable;

class AutoDisableInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:disable_inactive_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User account must be at least x days old & user account x days Of inactivity to be disabled';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        if (!config('pruning.user_pruning')) {
            return;
        }

        $disabledGroupId = Group::where('slug', '=', 'disabled')->soleValue('id');

        $current = Carbon::now();

        User::query()
            ->whereIntegerInRaw('group_id', config('pruning.group_ids'))
            ->where('created_at', '<', $current->copy()->subDays(config('pruning.account_age')))
            ->where('last_login', '<', $current->copy()->subDays(config('pruning.last_login')))
            ->whereDoesntHave('seedingTorrents')
            ->chunk(100, function ($users) use ($disabledGroupId): void {
                foreach ($users as $user) {
                    $user->update([
                        'group_id'     => $disabledGroupId,
                        'can_download' => false,
                        'disabled_at'  => Carbon::now(),
                    ]);

                    cache()->forget('user:'.$user->passkey);

                    Unit3dAnnounce::addUser($user);

                    // Send Email
                    dispatch(new SendDisableUserMail($user));
                }
            });

        $this->comment('Automated user disable command complete');
    }
}
