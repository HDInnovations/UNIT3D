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
use App\Models\User;
use App\Models\Warning;
use App\Notifications\UserWarning;
use App\Services\Unit3dAnnounce;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('auto:warning')]
#[Description('Automatically post warnings to users accounts and warnings table')]
class AutoWarning extends Command
{
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

        $hitrun = History::query()
            ->with(['user', 'torrent'])
            ->where('actual_downloaded', '>', 0)
            ->where('prewarned_at', '<=', now()->subDays(config('hitrun.prewarn')))
            ->where('hitrun', '=', 0)
            ->where('immune', '=', 0)
            ->where('active', '=', 0)
            ->where('seedtime', '<', config('hitrun.seedtime'))
            ->where('updated_at', '<', now()->subDays(config('hitrun.grace')))
            ->whereRelation('user.group', 'is_immune', '=', false)
            ->whereRelation('user', 'is_donor', '=', false)
            ->whereHas('torrent', fn ($query) => $query->whereRaw('history.actual_downloaded > torrents.size * ?', [config('hitrun.buffer') / 100]))
            ->whereDoesntHave('user.warnings', fn ($query) => $query->withTrashed()->whereColumn('warnings.torrent_id', '=', 'history.torrent_id'))
            ->get();

        $usersWithWarnings = [];

        foreach ($hitrun as $hr) {
            Warning::query()->create([
                'user_id'    => $hr->user->id,
                'warned_by'  => User::SYSTEM_USER_ID,
                'torrent_id' => $hr->torrent->id,
                'reason'     => \sprintf('Hit and Run Warning For Torrent %s', $hr->torrent->name),
                'expires_on' => now()->addDays(config('hitrun.expire')),
                'active'     => true,
            ]);

            History::query()
                ->where('torrent_id', '=', $hr->torrent_id)
                ->where('user_id', '=', $hr->user_id)
                ->update([
                    'hitrun'     => true,
                    'updated_at' => DB::raw('updated_at'),
                ]);

            // Add +1 To Users Warnings Count In Users Table
            $hr->user->increment('hitandruns');

            // Add user to usersWithWarnings array
            $usersWithWarnings[$hr->user->id] = $hr->user;
        }

        // Send a single notification for each user with warnings
        foreach ($usersWithWarnings as $user) {
            $user->notify(new UserWarning($user));
        }

        // Calculate User Warning Count and Disable DL Priv If Required.
        Warning::query()
            ->with('user')
            ->select(DB::raw('user_id, count(*) as value'))
            ->where('active', '=', 1)
            ->groupBy('user_id')
            ->having('value', '>=', config('hitrun.max_warnings'))
            ->whereRelation('user', 'can_download', '=', true)
            ->eachById(function ($warning): void {
                $warning->user->update(['can_download' => 0]);

                cache()->forget('user:'.$warning->user->passkey);

                Unit3dAnnounce::addUser($warning->user);
            }, 100, 'user_id');

        $this->comment('Automated user warning command complete');
    }
}
