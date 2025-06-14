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
use Illuminate\Support\Facades\Schedule;
use Spatie\Backup\Commands\BackupCommand;
use Spatie\Backup\Commands\CleanupCommand;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

if (! config('announce.external_tracker.is_enabled')) {
    Schedule::command(AutoUpsertPeers::class)->everyFiveSeconds()->withoutOverlapping(2);
    Schedule::command(AutoUpsertHistories::class)->everyFiveSeconds()->withoutOverlapping(2);
    Schedule::command(AutoUpsertAnnounces::class)->everyFiveSeconds()->withoutOverlapping(2);
    Schedule::command(AutoCacheUserLeechCounts::class)->everyThirtyMinutes();
    Schedule::command(SyncPeers::class)->everyFiveMinutes();
    Schedule::command(AutoTorrentBalance::class)->hourly();
}

Schedule::command(AutoUpdateUserLastActions::class)->everyFiveSeconds();
Schedule::command(AutoDeleteStoppedPeers::class)->everyTwoMinutes();
Schedule::command(AutoUnbookmarkCompletedTorrents::class)->everyFifteenMinutes();
Schedule::command(AutoGroup::class)->daily();
Schedule::command(AutoNerdStat::class)->hourly();
Schedule::command(AutoCacheRandomMediaIds::class)->hourly();
Schedule::command(AutoRewardResurrection::class)->daily();
Schedule::command(AutoHighspeedTag::class)->hourly();
Schedule::command(AutoPreWarning::class)->hourly();
Schedule::command(AutoWarning::class)->daily();
Schedule::command(AutoDeactivateWarning::class)->hourly();
Schedule::command(AutoFlushPeers::class)->hourly();
Schedule::command(AutoBonAllocation::class)->hourly();
Schedule::command(AutoRemovePersonalFreeleech::class)->hourly();
Schedule::command(AutoRemoveFeaturedTorrent::class)->hourly();
Schedule::command(AutoRecycleInvites::class)->daily();
Schedule::command(AutoRecycleAudits::class)->daily();
Schedule::command(AutoRecycleFailedLogins::class)->daily();
Schedule::command(AutoDisableInactiveUsers::class)->daily();
Schedule::command(AutoSoftDeleteDisabledUsers::class)->daily();
Schedule::command(AutoRecycleClaimedTorrentRequests::class)->daily();
Schedule::command(DeleteUnparticipatedConversations::class)->daily();
Schedule::command(AutoCorrectHistory::class)->daily();
Schedule::command(EmailBlacklistUpdate::class)->weekends();
Schedule::command(AutoResetUserFlushes::class)->daily();
Schedule::command(AutoRemoveTimedTorrentBuffs::class)->hourly();
Schedule::command(AutoRefundDownload::class)->daily();
Schedule::command(ClearResetsCommand::class)->daily();
Schedule::command(AutoSyncTorrentsToMeilisearch::class)->everyFifteenMinutes();
Schedule::command(AutoSyncPeopleToMeilisearch::class)->daily();
Schedule::command(AutoRemoveExpiredDonors::class)->daily();
// $schedule->command(AutoBanDisposableUsers::class)->weekends();
Schedule::command(CleanupCommand::class)->daily();
Schedule::command(BackupCommand::class, ['--only-db'])->daily();
Schedule::command(BackupCommand::class, ['--only-files'])->daily();
