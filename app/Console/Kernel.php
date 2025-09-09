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

namespace App\Console;

use App\Console\Commands\AutoBonAllocation;
use App\Console\Commands\AutoCacheRandomMediaIds;
use App\Console\Commands\AutoCacheUserLeechCounts;
use App\Console\Commands\AutoCorrectHistory;
use App\Console\Commands\AutoDeactivateWarning;
use App\Console\Commands\AutoDeleteStoppedPeers;
use App\Console\Commands\AutoDisableInactiveUsers;
use App\Console\Commands\AutoFlushPeers;
use App\Console\Commands\AutoGroup;
use App\Console\Commands\AutoHighspeedTag;
use App\Console\Commands\AutoNerdStat;
use App\Console\Commands\AutoPreWarning;
use App\Console\Commands\AutoRecycleAudits;
use App\Console\Commands\AutoRecycleClaimedTorrentRequests;
use App\Console\Commands\AutoRecycleFailedLogins;
use App\Console\Commands\AutoRecycleInvites;
use App\Console\Commands\AutoRefundDownload;
use App\Console\Commands\AutoRemoveExpiredDonors;
use App\Console\Commands\AutoRemoveFeaturedTorrent;
use App\Console\Commands\AutoRemovePersonalFreeleech;
use App\Console\Commands\AutoRemoveReseeds;
use App\Console\Commands\AutoRemoveTimedTorrentBuffs;
use App\Console\Commands\AutoResetUserFlushes;
use App\Console\Commands\AutoRewardResurrection;
use App\Console\Commands\AutoSoftDeleteDisabledUsers;
use App\Console\Commands\AutoSyncPeopleToMeilisearch;
use App\Console\Commands\AutoSyncTorrentsToMeilisearch;
use App\Console\Commands\AutoTorrentBalance;
use App\Console\Commands\AutoUnbookmarkCompletedTorrents;
use App\Console\Commands\AutoUpdateUserLastActions;
use App\Console\Commands\AutoUpsertAnnounces;
use App\Console\Commands\AutoUpsertHistories;
use App\Console\Commands\AutoUpsertPeers;
use App\Console\Commands\AutoWarning;
use App\Console\Commands\DeleteUnparticipatedConversations;
use App\Console\Commands\EmailBlacklistUpdate;
use App\Console\Commands\SyncPeers;
use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Backup\Commands\BackupCommand;
use Spatie\Backup\Commands\CleanupCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        if (! config('announce.external_tracker.is_enabled')) {
            $schedule->command(AutoUpsertPeers::class)->everyFiveSeconds()->withoutOverlapping(2);
            $schedule->command(AutoUpsertHistories::class)->everyFiveSeconds()->withoutOverlapping(2);
            $schedule->command(AutoUpsertAnnounces::class)->everyFiveSeconds()->withoutOverlapping(2);
            $schedule->command(AutoCacheUserLeechCounts::class)->everyThirtyMinutes();
            $schedule->command(SyncPeers::class)->everyFiveMinutes();
            $schedule->command(AutoTorrentBalance::class)->hourly();
        }

        $schedule->command(AutoUpdateUserLastActions::class)->everyFiveSeconds();
        $schedule->command(AutoDeleteStoppedPeers::class)->everyTwoMinutes();
        $schedule->command(AutoUnbookmarkCompletedTorrents::class)->everyFifteenMinutes();
        $schedule->command(AutoGroup::class)->daily();
        $schedule->command(AutoNerdStat::class)->hourly();
        $schedule->command(AutoCacheRandomMediaIds::class)->hourly();
        $schedule->command(AutoRewardResurrection::class)->daily();
        $schedule->command(AutoHighspeedTag::class)->hourly();
        $schedule->command(AutoPreWarning::class)->hourly();
        $schedule->command(AutoWarning::class)->daily();
        $schedule->command(AutoDeactivateWarning::class)->hourly();
        $schedule->command(AutoFlushPeers::class)->hourly();
        $schedule->command(AutoBonAllocation::class)->hourly();
        $schedule->command(AutoRemovePersonalFreeleech::class)->hourly();
        $schedule->command(AutoRemoveFeaturedTorrent::class)->hourly();
        $schedule->command(AutoRecycleInvites::class)->daily();
        $schedule->command(AutoRecycleAudits::class)->daily();
        $schedule->command(AutoRecycleFailedLogins::class)->daily();
        $schedule->command(AutoDisableInactiveUsers::class)->daily();
        $schedule->command(AutoSoftDeleteDisabledUsers::class)->daily();
        $schedule->command(AutoRecycleClaimedTorrentRequests::class)->daily();
        $schedule->command(DeleteUnparticipatedConversations::class)->daily();
        $schedule->command(AutoCorrectHistory::class)->daily();
        $schedule->command(EmailBlacklistUpdate::class)->weekends();
        $schedule->command(AutoResetUserFlushes::class)->daily();
        $schedule->command(AutoRemoveTimedTorrentBuffs::class)->hourly();
        $schedule->command(AutoRefundDownload::class)->daily();
        $schedule->command(ClearResetsCommand::class)->daily();
        $schedule->command(AutoSyncTorrentsToMeilisearch::class)->everyFifteenMinutes();
        $schedule->command(AutoSyncPeopleToMeilisearch::class)->daily();
        $schedule->command(AutoRemoveExpiredDonors::class)->daily();
        $schedule->command(AutoRemoveReseeds::class)->daily();
        // $schedule->command(AutoBanDisposableUsers::class)->weekends();
        $schedule->command(CleanupCommand::class)->daily();
        $schedule->command(BackupCommand::class, ['--only-db'])->daily();
        $schedule->command(BackupCommand::class, ['--only-files'])->daily();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
