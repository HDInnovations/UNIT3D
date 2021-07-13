<?php

use App\Http\Controllers\AchievementsController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AnnounceController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BanController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\ChatStatusController;
use App\Http\Controllers\CheaterController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FlushController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ForumCategoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\GraveyardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MassActionController;
use App\Http\Controllers\MediaLanguageController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\PlaylistTorrentController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SeedboxController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubtitleController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TopicLabelController;
use App\Http\Controllers\TorrentController;
use App\Http\Controllers\TvSeasonController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\WishController;
use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(['middleware' => 'language'], function () {
    /*
    |---------------------------------------------------------------------------------
    | Website (Not Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['before' => 'auth', 'middleware' => 'guest'], function () {
        // Activation
        Route::get('/activate/{token}', [Auth\ActivationController::class, 'activate'])->name('activate');

        // Application Signup
        Route::get('/application', [Auth\ApplicationController::class, 'create'])->name('application.create');
        Route::post('/application', [Auth\ApplicationController::class, 'store'])->name('application.store');

        // Authentication
        Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [Auth\LoginController::class, 'login'])->name('');

        // Forgot Username
        Route::get('username/reminder', [Auth\ForgotUsernameController::class, 'showForgotUsernameForm'])->name('username.request');
        Route::post('username/reminder', [Auth\ForgotUsernameController::class, 'sendUsernameReminder'])->name('username.email');

        // Password Reset
        Route::post('password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset', [Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/reset', [Auth\ResetPasswordController::class, 'reset'])->name('');
        Route::get('/password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');

        // Registration
        Route::get('/register/{code?}', [Auth\RegisterController::class, 'registrationForm'])->name('registrationForm');
        Route::post('/register/{code?}', [Auth\RegisterController::class, 'register'])->name('register');
    });

    /*
    |---------------------------------------------------------------------------------
    | Website (Authorized By Key) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['before' => 'auth'], function () {
        // Announce (Pass Key Auth)
        Route::get('/announce/{passkey}', [AnnounceController::class, 'index'])->name('announce');

        // RSS (RSS Key Auth)
        Route::get('/rss/{id}.{rsskey}', [RssController::class, 'show'])->name('rss.show.rsskey');
        Route::get('/torrent/download/{id}.{rsskey}', [TorrentController::class, 'download'])->name('torrent.download.rsskey');
    });

    /*
    |---------------------------------------------------------------------------------
    | Website (When Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['auth', 'twostep', 'banned']], function () {

        // General
        Route::get('/logout', [Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('/', [HomeController::class, 'index'])->name('home.index');

        // Achievements System
        Route::group(['prefix' => 'achievements'], function () {
            Route::name('achievements.')->group(function () {
                Route::get('/', [AchievementsController::class, 'index'])->name('index');
                Route::get('/{username}', [AchievementsController::class, 'show'])->name('show');
            });
        });

        // Albums System
        Route::group(['prefix' => 'albums'], function () {
            Route::name('albums.')->group(function () {
                Route::get('/', [AlbumController::class, 'index'])->name('index');
                Route::get('/create', [AlbumController::class, 'create'])->name('create');
                Route::post('/store', [AlbumController::class, 'store'])->name('store');
                Route::get('/{id}', [AlbumController::class, 'show'])->name('show');
                Route::delete('/{id}/destroy', [AlbumController::class, 'destroy'])->name('destroy');
            });
        });

        // Articles System
        Route::group(['prefix' => 'articles'], function () {
            Route::name('articles.')->group(function () {
                Route::get('/', [ArticleController::class, 'index'])->name('index');
                Route::get('/{id}', [ArticleController::class, 'show'])->name('show');
            });
        });

        // RSS System
        Route::group(['prefix' => 'rss'], function () {
            Route::name('rss.')->group(function () {
                Route::get('/', [RssController::class, 'index'])->name('index');
                Route::get('/create', [RssController::class, 'create'])->name('create');
                Route::post('/store', [RssController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [RssController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [RssController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [RssController::class, 'destroy'])->name('destroy');
            });
        });

        // TwoStep Auth System
        Route::group(['prefix' => 'twostep'], function () {
            Route::get('/needed', [Auth\TwoStepController::class, 'showVerification'])->name('verificationNeeded');
            Route::post('/verify', [Auth\TwoStepController::class, 'verify'])->name('verify');
            Route::post('/resend', [Auth\TwoStepController::class, 'resend'])->name('resend');
        });

        // Bonus System
        Route::group(['prefix' => 'bonus'], function () {
            Route::get('/', [BonusController::class, 'bonus'])->name('bonus');
            Route::get('/gifts', [BonusController::class, 'gifts'])->name('bonus_gifts');
            Route::get('/tips', [BonusController::class, 'tips'])->name('bonus_tips');
            Route::get('/store', [BonusController::class, 'store'])->name('bonus_store');
            Route::get('/gift', [BonusController::class, 'gift'])->name('bonus_gift');
            Route::post('/exchange/{id}', [BonusController::class, 'exchange'])->name('bonus_exchange');
            Route::post('/gift', [BonusController::class, 'sendGift'])->name('bonus_send_gift');
        });

        // Reports System
        Route::group(['prefix' => 'reports'], function () {
            Route::post('/torrent/{id}', [ReportController::class, 'torrent'])->name('report_torrent');
            Route::post('/request/{id}', [ReportController::class, 'request'])->name('report_request');
            Route::post('/user/{username}', [ReportController::class, 'user'])->name('report_user');
        });

        // Categories System
        Route::group(['prefix' => 'categories'], function () {
            Route::name('categories.')->group(function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
            });
        });

        // Contact Us System
        Route::group(['prefix' => 'contact'], function () {
            Route::name('contact.')->group(function () {
                Route::get('/', [ContactController::class, 'index'])->name('index');
                Route::post('/store', [ContactController::class, 'store'])->name('store');
            });
        });

        // Pages System
        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', [PageController::class, 'index'])->name('pages.index');
            Route::get('/staff', [PageController::class, 'staff'])->name('staff');
            Route::get('/internal', [PageController::class, 'internal'])->name('internal');
            Route::get('/blacklist', [PageController::class, 'blacklist'])->name('blacklist');
            Route::get('/aboutus', [PageController::class, 'about'])->name('about');
            Route::get('/{id}', [PageController::class, 'show'])->where('id', '[0-9]+')->name('pages.show');
        });

        // Comments System
        Route::group(['prefix' => 'comments'], function () {
            Route::post('/article/{id}', [CommentController::class, 'article'])->name('comment_article');
            Route::post('/torrent/{id}', [CommentController::class, 'torrent'])->name('comment_torrent');
            Route::get('/thanks/{id}', [CommentController::class, 'quickthanks'])->name('comment_thanks');
            Route::post('/request/{id}', [CommentController::class, 'request'])->name('comment_request');
            Route::post('/playlist/{id}', [CommentController::class, 'playlist'])->name('comment_playlist');
            Route::post('/collection/{id}', [CommentController::class, 'collection'])->name('comment_collection');
            Route::post('/ticket/{id}', [CommentController::class, 'ticket'])->name('comment_ticket');
            Route::post('/edit/{comment_id}', [CommentController::class, 'editComment'])->name('comment_edit');
            Route::get('/delete/{comment_id}', [CommentController::class, 'deleteComment'])->name('comment_delete');
        });

        // Extra-Stats System
        Route::group(['prefix' => 'stats'], function () {
            Route::get('/', [StatsController::class, 'index'])->name('stats');
            Route::get('/user/uploaded', [StatsController::class, 'uploaded'])->name('uploaded');
            Route::get('/user/downloaded', [StatsController::class, 'downloaded'])->name('downloaded');
            Route::get('/user/seeders', [StatsController::class, 'seeders'])->name('seeders');
            Route::get('/user/leechers', [StatsController::class, 'leechers'])->name('leechers');
            Route::get('/user/uploaders', [StatsController::class, 'uploaders'])->name('uploaders');
            Route::get('/user/bankers', [StatsController::class, 'bankers'])->name('bankers');
            Route::get('/user/seedtime', [StatsController::class, 'seedtime'])->name('seedtime');
            Route::get('/user/seedsize', [StatsController::class, 'seedsize'])->name('seedsize');
            Route::get('/torrent/seeded', [StatsController::class, 'seeded'])->name('seeded');
            Route::get('/torrent/leeched', [StatsController::class, 'leeched'])->name('leeched');
            Route::get('/torrent/completed', [StatsController::class, 'completed'])->name('completed');
            Route::get('/torrent/dying', [StatsController::class, 'dying'])->name('dying');
            Route::get('/torrent/dead', [StatsController::class, 'dead'])->name('dead');
            Route::get('/request/bountied', [StatsController::class, 'bountied'])->name('bountied');
            Route::get('/groups', [StatsController::class, 'groups'])->name('groups');
            Route::get('/groups/group/{id}', [StatsController::class, 'group'])->name('group');
            Route::get('/languages', [StatsController::class, 'languages'])->name('languages');
        });

        // Private Messages System
        Route::group(['prefix' => 'mail'], function () {
            Route::post('/searchPMInbox', [PrivateMessageController::class, 'searchPMInbox'])->name('searchPMInbox');
            Route::post('/searchPMOutbox', [PrivateMessageController::class, 'searchPMOutbox'])->name('searchPMOutbox');
            Route::get('/inbox', [PrivateMessageController::class, 'getPrivateMessages'])->name('inbox');
            Route::get('/message/{id}', [PrivateMessageController::class, 'getPrivateMessageById'])->name('message');
            Route::get('/outbox', [PrivateMessageController::class, 'getPrivateMessagesSent'])->name('outbox');
            Route::get('/create', [PrivateMessageController::class, 'makePrivateMessage'])->name('create');
            Route::get('/mark-all-read', [PrivateMessageController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/empty-inbox', [PrivateMessageController::class, 'emptyInbox'])->name('empty-inbox');
            Route::post('/send', [PrivateMessageController::class, 'sendPrivateMessage'])->name('send-pm');
            Route::post('/{id}/reply', [PrivateMessageController::class, 'replyPrivateMessage'])->name('reply-pm');
            Route::post('/{id}/destroy', [PrivateMessageController::class, 'deletePrivateMessage'])->name('delete-pm');
        });

        // Requests System
        Route::group(['prefix' => 'requests'], function () {
            Route::name('requests.')->group(function () {
                Route::get('/', [RequestController::class, 'index'])->name('index');
            });
        });

        Route::group(['prefix' => 'requests'], function () {
            Route::get('/add/{title?}/{imdb?}/{tmdb?}', [RequestController::class, 'addRequestForm'])->name('add_request_form');
            Route::post('/add', [RequestController::class, 'addRequest'])->name('add_request');
            Route::get('/{id}/edit', [RequestController::class, 'editRequestForm'])->name('edit_request_form');
            Route::post('/{id}/edit', [RequestController::class, 'editRequest'])->name('edit_request');
            Route::get('/{id}{hash?}', [RequestController::class, 'request'])->name('request');
            Route::get('/{id}/accept', [RequestController::class, 'approveRequest'])->name('approveRequest');
            Route::post('/{id}/delete', [RequestController::class, 'deleteRequest'])->name('deleteRequest');
            Route::post('/{id}/fill', [RequestController::class, 'fillRequest'])->name('fill_request');
            Route::get('/{id}/reject', [RequestController::class, 'rejectRequest'])->name('rejectRequest');
            Route::post('/{id}/vote', [RequestController::class, 'addBonus'])->name('add_votes');
            Route::post('/{id}/claim', [RequestController::class, 'claimRequest'])->name('claimRequest');
            Route::get('/{id}/unclaim', [RequestController::class, 'unclaimRequest'])->name('unclaimRequest');
            Route::get('/{id}/reset', [RequestController::class, 'resetRequest'])->name('resetRequest')->middleware('modo');
        });

        // Torrents System
        Route::group(['prefix' => 'upload'], function () {
            Route::get('/{category_id}/{title?}/{imdb?}/{tmdb?}', [TorrentController::class, 'uploadForm'])->name('upload_form');
            Route::post('/', [TorrentController::class, 'upload'])->name('upload');
            Route::post('/preview', [TorrentController::class, 'preview']);
        });

        Route::group(['prefix' => 'torrents'], function () {
            Route::get('/filter', [TorrentController::class, 'faceted']);
            Route::get('/filterSettings', [TorrentController::class, 'filtered']);
            Route::get('/', [TorrentController::class, 'torrents'])->name('torrents');
            Route::get('/{id}{hash?}', [TorrentController::class, 'torrent'])->name('torrent');
            Route::get('/{id}/peers', [TorrentController::class, 'peers'])->name('peers');
            Route::get('/{id}/history', [TorrentController::class, 'history'])->name('history');
            Route::get('/download_check/{id}', [TorrentController::class, 'downloadCheck'])->name('download_check');
            Route::get('/download/{id}', [TorrentController::class, 'download'])->name('download');
            Route::get('/view/cards', [TorrentController::class, 'cardLayout'])->name('cards');
            Route::get('/view/groupings', [TorrentController::class, 'groupingLayout'])->name('groupings');
            Route::post('/delete', [TorrentController::class, 'deleteTorrent'])->name('delete');
            Route::get('/{id}/edit', [TorrentController::class, 'editForm'])->name('edit_form');
            Route::post('/{id}/edit', [TorrentController::class, 'edit'])->name('edit');
            Route::get('/{id}/torrent_fl', [TorrentController::class, 'grantFL'])->name('torrent_fl');
            Route::get('/{id}/torrent_doubleup', [TorrentController::class, 'grantDoubleUp'])->name('torrent_doubleup');
            Route::get('/{id}/bumpTorrent', [TorrentController::class, 'bumpTorrent'])->name('bumpTorrent');
            Route::get('/{id}/torrent_sticky', [TorrentController::class, 'sticky'])->name('torrent_sticky');
            Route::get('/{id}/torrent_feature', [TorrentController::class, 'grantFeatured'])->name('torrent_feature');
            Route::get('/{id}/torrent_revokefeature', [TorrentController::class, 'revokeFeatured'])->name('torrent_revokefeature');
            Route::get('/{id}/reseed', [TorrentController::class, 'reseedTorrent'])->name('reseed');
            Route::post('/{id}/tip_uploader', [BonusController::class, 'tipUploader'])->name('tip_uploader');
            Route::get('/{id}/freeleech_token', [TorrentController::class, 'freeleechToken'])->name('freeleech_token');
            Route::get('/similar/{category_id}.{tmdb}', [TorrentController::class, 'similar'])->name('torrents.similar');
        });

        // Warnings System
        Route::group(['prefix' => 'warnings'], function () {
            Route::get('/{id}/deactivate', [WarningController::class, 'deactivate'])->name('deactivateWarning');
            Route::get('/{username}/mass-deactivate', [WarningController::class, 'deactivateAllWarnings'])->name('massDeactivateWarnings');
            Route::delete('/{id}', [WarningController::class, 'deleteWarning'])->name('deleteWarning');
            Route::delete('/{username}/mass-delete', [WarningController::class, 'deleteAllWarnings'])->name('massDeleteWarnings');
            Route::get('/{id}/restore', [WarningController::class, 'restoreWarning'])->name('restoreWarning');
            Route::get('/{username}', [WarningController::class, 'show'])->name('warnings.show');
        });

        // Users System
        Route::group(['prefix' => 'users'], function () {
            Route::get('/{username}', [UserController::class, 'show'])->name('users.show');
            Route::get('/{username}/edit', [UserController::class, 'editProfileForm'])->name('user_edit_profile_form');
            Route::post('/{username}/edit', [UserController::class, 'editProfile'])->name('user_edit_profile');
            Route::post('/{username}/photo', [UserController::class, 'changePhoto'])->name('user_change_photo');
            Route::get('/{username}/banlog', [UserController::class, 'getBans'])->name('banlog');
            Route::post('/{username}/userFilters', [UserController::class, 'myFilter'])->name('myfilter');
            Route::get('/{username}/downloadHistoryTorrents', [UserController::class, 'downloadHistoryTorrents'])->name('download_history_torrents');
            Route::get('/{username}/seeds', [UserController::class, 'seeds'])->name('user_seeds');
            Route::get('/{username}/flushOwnGhostPeers', [UserController::class, 'flushOwnGhostPeers'])->name('flush_own_ghost_peers');
            Route::get('/{username}/resurrections', [UserController::class, 'resurrections'])->name('user_resurrections');
            Route::get('/{username}/requested', [UserController::class, 'requested'])->name('user_requested');
            Route::get('/{username}/active', [UserController::class, 'active'])->name('user_active');
            Route::get('/{username}/torrents', [UserController::class, 'torrents'])->name('user_torrents');
            Route::get('/{username}/uploads', [UserController::class, 'uploads'])->name('user_uploads');
            Route::get('/{username}/downloads', [UserController::class, 'downloads'])->name('user_downloads');
            Route::get('/{username}/unsatisfieds', [UserController::class, 'unsatisfieds'])->name('user_unsatisfieds');
            Route::get('/{username}/topics', [UserController::class, 'topics'])->name('user_topics');
            Route::get('/{username}/posts', [UserController::class, 'posts'])->name('user_posts');
            Route::get('/{username}/followers', [UserController::class, 'followers'])->name('user_followers');
            Route::get('/{username}/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

            Route::get('/{username}/settings', [UserController::class, 'settings'])->name('user_settings');
            Route::get('/{username}/settings/privacy{hash?}', [UserController::class, 'privacy'])->name('user_privacy');
            Route::get('/{username}/settings/security{hash?}', [UserController::class, 'security'])->name('user_security');
            Route::get('/{username}/settings/notification{hash?}', [UserController::class, 'notification'])->name('user_notification');
            Route::post('/{username}/settings/change_settings', [UserController::class, 'changeSettings'])->name('change_settings');
            Route::post('/{username}/settings/change_password', [UserController::class, 'changePassword'])->name('change_password');
            Route::post('/{username}/settings/change_email', [UserController::class, 'changeEmail'])->name('change_email');
            Route::post('/{username}/settings/change_pid', [UserController::class, 'changePID'])->name('change_pid');
            Route::post('/{username}/settings/change_rid', [UserController::class, 'changeRID'])->name('change_rid');
            Route::post('/{username}/settings/change_api_token', [UserController::class, 'changeApiToken'])->name('change_api_token');
            Route::get('/{username}/settings/notification/disable', [UserController::class, 'disableNotifications'])->name('notification_disable');
            Route::get('/{username}/settings/notification/enable', [UserController::class, 'enableNotifications'])->name('notification_enable');
            Route::post('/{username}/settings/notification/account', [UserController::class, 'changeAccountNotification'])->name('notification_account');
            Route::post('/{username}/settings/notification/following', [UserController::class, 'changeFollowingNotification'])->name('notification_following');
            Route::post('/{username}/settings/notification/forum', [UserController::class, 'changeForumNotification'])->name('notification_forum');
            Route::post('/{username}/settings/notification/subscription', [UserController::class, 'changeSubscriptionNotification'])->name('notification_subscription');
            Route::post('/{username}/settings/notification/mention', [UserController::class, 'changeMentionNotification'])->name('notification_mention');
            Route::post('/{username}/settings/notification/torrent', [UserController::class, 'changeTorrentNotification'])->name('notification_torrent');
            Route::post('/{username}/settings/notification/bon', [UserController::class, 'changeBonNotification'])->name('notification_bon');
            Route::post('/{username}/settings/notification/request', [UserController::class, 'changeRequestNotification'])->name('notification_request');
            Route::post('/{username}/settings/privacy/profile', [UserController::class, 'changeProfile'])->name('privacy_profile');
            Route::post('/{username}/settings/privacy/forum', [UserController::class, 'changeForum'])->name('privacy_forum');
            Route::post('/{username}/settings/privacy/torrent', [UserController::class, 'changeTorrent'])->name('privacy_torrent');
            Route::post('/{username}/settings/privacy/follower', [UserController::class, 'changeFollower'])->name('privacy_follower');
            Route::post('/{username}/settings/privacy/achievement', [UserController::class, 'changeAchievement'])->name('privacy_achievement');
            Route::post('/{username}/settings/privacy/request', [UserController::class, 'changeRequest'])->name('privacy_request');
            Route::post('/{username}/settings/privacy/other', [UserController::class, 'changeOther'])->name('privacy_other');
            Route::post('/{username}/settings/change_twostep', [UserController::class, 'changeTwoStep'])->name('change_twostep');
            Route::get('/{username}/settings/hidden', [UserController::class, 'makeHidden'])->name('user_hidden');
            Route::get('/{username}/settings/visible', [UserController::class, 'makeVisible'])->name('user_visible');
            Route::get('/{username}/settings/private', [UserController::class, 'makePrivate'])->name('user_private');
            Route::get('/{username}/settings/public', [UserController::class, 'makePublic'])->name('user_public');
            Route::post('/accept-rules', [UserController::class, 'acceptRules'])->name('accept.rules');
            Route::get('/{username}/seedboxes', [SeedboxController::class, 'index'])->name('seedboxes.index');
            Route::post('/{username}/seedboxes', [SeedboxController::class, 'store'])->name('seedboxes.store');
            Route::delete('/seedboxes/{id}', [SeedboxController::class, 'destroy'])->name('seedboxes.destroy');
        });

        // Wishlist System
        Route::group(['prefix' => 'wishes'], function () {
            Route::name('wishes.')->group(function () {
                Route::get('/{username}', [WishController::class, 'index'])->name('index');
                Route::post('/store', [WishController::class, 'store'])->name('store');
                Route::get('/{id}/destroy', [WishController::class, 'destroy'])->name('destroy');
            });
        });

        // Follow System
        Route::group(['prefix' => 'follow'], function () {
            Route::name('follow.')->group(function () {
                Route::post('/{username}', [FollowController::class, 'store'])->name('store');
                Route::delete('/{username}', [FollowController::class, 'destroy'])->name('destroy');
            });
        });

        // Invite System
        Route::group(['prefix' => 'invites'], function () {
            Route::name('invites.')->group(function () {
                Route::get('/create', [InviteController::class, 'create'])->name('create');
                Route::post('/store', [InviteController::class, 'store'])->name('store');
                Route::post('/{id}/send', [InviteController::class, 'send'])->where('id', '[0-9]+')->name('send');
                Route::get('/{username}', [InviteController::class, 'index'])->name('index');
            });
        });

        // Poll System
        Route::group(['prefix' => 'polls'], function () {
            Route::get('/', [PollController::class, 'index'])->name('polls');
            Route::post('/vote', [PollController::class, 'vote']);
            Route::get('/{id}', [PollController::class, 'show'])->where('id', '[0-9]+')->name('poll');
            Route::get('/{id}/result', [PollController::class, 'result'])->name('poll_results');
        });

        // Graveyard System
        Route::group(['prefix' => 'graveyard'], function () {
            Route::name('graveyard.')->group(function () {
                Route::get('/', [GraveyardController::class, 'index'])->name('index');
                Route::post('/{id}/store', [GraveyardController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [GraveyardController::class, 'destroy'])->name('destroy');
            });
        });

        // Notifications System
        Route::group(['prefix' => 'notifications'], function () {
            Route::name('notifications.')->group(function () {
                Route::get('/filter', [NotificationController::class, 'faceted']);
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::post('/{id}/update', [NotificationController::class, 'update'])->name('update');
                Route::post('/updateall', [NotificationController::class, 'updateAll'])->name('updateall');
                Route::delete('/{id}/destroy', [NotificationController::class, 'destroy'])->name('destroy');
                Route::delete('/destroyall', [NotificationController::class, 'destroyAll'])->name('destroyall');
                Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
            });
        });

        // Images System
        Route::group(['prefix' => 'images'], function () {
            Route::name('images.')->group(function () {
                Route::get('/{id}/create', [ImageController::class, 'create'])->name('create');
                Route::post('/store', [ImageController::class, 'store'])->name('store');
                Route::get('/{id}/download', [ImageController::class, 'download'])->name('download');
                Route::delete('/{id}/destroy', [ImageController::class, 'destroy'])->name('destroy');
            });
        });

        // Playlist System
        Route::group(['prefix' => 'playlists'], function () {
            Route::name('playlists.')->group(function () {
                Route::get('/', [PlaylistController::class, 'index'])->name('index');
                Route::get('/create', [PlaylistController::class, 'create'])->name('create');
                Route::post('/store', [PlaylistController::class, 'store'])->name('store');
                Route::get('/{id}', [PlaylistController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/{id}/edit', [PlaylistController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [PlaylistController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [PlaylistController::class, 'destroy'])->name('destroy');
                Route::post('/attach', [PlaylistTorrentController::class, 'store'])->name('attach');
                Route::delete('/{id}/detach', [PlaylistTorrentController::class, 'destroy'])->name('detach');
                Route::get('/{id}/download', [PlaylistController::class, 'downloadPlaylist'])->name('download');
            });
        });

        // Subtitles System
        Route::group(['prefix' => 'subtitles'], function () {
            Route::name('subtitles.')->group(function () {
                Route::get('/', [SubtitleController::class, 'index'])->name('index');
                Route::get('/create/{torrent_id}', [SubtitleController::class, 'create'])->where('id', '[0-9]+')->name('create');
                Route::post('/store', [SubtitleController::class, 'store'])->name('store');
                Route::post('/{id}/update', [SubtitleController::class, 'update'])->name('update');
                Route::delete('/{id}/delete', [SubtitleController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/download', [SubtitleController::class, 'download'])->name('download');
            });
        });

        // Tickets System
        Route::group(['prefix' => 'tickets'], function () {
            Route::name('tickets.')->group(function () {
                Route::get('/', [TicketController::class, 'index'])->name('index');
                Route::get('/create', [TicketController::class, 'create'])->name('create');
                Route::post('/store', [TicketController::class, 'store'])->name('store');
                Route::get('/{id}', [TicketController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/{id}/edit', [TicketController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [TicketController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [TicketController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/assign', [TicketController::class, 'assign'])->name('assign');
                Route::post('/{id}/unassign', [TicketController::class, 'unassign'])->name('unassign');
                Route::post('/{id}/close', [TicketController::class, 'close'])->name('close');
                Route::post('/attachments/{attachment}/download', [TicketAttachmentController::class, 'download'])->name('attachment.download');
            });
        });
    });

    /*
    |------------------------------------------
    | MediaHub (When Authorized)
    |------------------------------------------
    */
    Route::group(['prefix' => 'mediahub', 'middleware' => ['auth', 'twostep', 'banned']], function () {
        // MediaHub Home
        Route::get('/', [HomeController::class, 'index'])->name('mediahub.index');

        // Genres
        Route::get('/genres', [GenreController::class, 'index'])->name('mediahub.genres.index');

        // Genre
        Route::get('/genre/{id}', [GenreController::class, 'show'])->name('mediahub.genres.show');

        // Networks
        Route::get('/networks', [NetworkController::class, 'index'])->name('mediahub.networks.index');

        // Network
        Route::get('/network/{id}', [NetworkController::class, 'show'])->name('mediahub.networks.show');

        // Companies
        Route::get('/companies', [CompanyController::class, 'index'])->name('mediahub.companies.index');

        // Company
        Route::get('/company/{id}', [CompanyController::class, 'show'])->name('mediahub.companies.show');

        // TV Shows
        Route::get('/tv-shows', [TvShowController::class, 'index'])->name('mediahub.shows.index');

        // TV Show
        Route::get('/tv-show/{id}', [TvShowController::class, 'show'])->name('mediahub.shows.show');

        // TV Show Season
        Route::get('/tv-show/season/{id}', [TvSeasonController::class, 'show'])->name('mediahub.season.show');

        // Persons
        Route::get('/persons', [PersonController::class, 'index'])->name('mediahub.persons.index');

        // Person
        Route::get('/persons/{id}', [PersonController::class, 'show'])->name('mediahub.persons.show');

        // Collections
        Route::get('/collections', [CollectionController::class, 'index'])->name('mediahub.collections.index');

        // Collection
        Route::get('/collections/{id}', [CollectionController::class, 'show'])->name('mediahub.collections.show');

        // Movies
        Route::get('/movies', [MovieController::class, 'index'])->name('mediahub.movies.index');

        // Movie
        Route::get('/movies/{id}', [MovieController::class, 'show'])->name('mediahub.movies.show');
    });

    /*
    |---------------------------------------------------------------------------------
    | ChatBox Routes Group (When Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'chatbox', 'middleware' => ['auth', 'twostep', 'banned']], function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::get('/chatrooms', [ChatController::class, 'fetchChatrooms']);
        Route::post('/change-chatroom', [ChatController::class, 'changeChatroom']);
        Route::get('/messages', [ChatController::class, 'fetchMessages']);
        Route::post('/messages', [ChatController::class, 'sendMessage']);
    });

    /*
    |---------------------------------------------------------------------------------
    | Forums Routes Group (When Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'forums', 'middleware' => ['auth', 'twostep', 'banned']], function () {
        // Forum System
        Route::name('forums.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/{id}', [ForumController::class, 'show'])->where('id', '[0-9]+')->name('show');
        });

        // Forum Category System
        Route::group(['prefix' => 'categories'], function () {
            Route::name('forums.categories.')->group(function () {
                Route::get('/{id}', [ForumCategoryController::class, 'show'])->where('id', '[0-9]+')->name('show');
            });
        });

        // Posts System
        Route::group(['prefix' => 'posts'], function () {
            Route::post('/topic/{id}/reply', [PostController::class, 'reply'])->name('forum_reply');
            Route::get('/posts/{id}/post-{postId}/edit', [PostController::class, 'postEditForm'])->name('forum_post_edit_form');
            Route::post('/posts/{postId}/edit', [PostController::class, 'postEdit'])->name('forum_post_edit');
            Route::get('/posts/{postId}/delete', [PostController::class, 'postDelete'])->name('forum_post_delete');
        });

        // Search Forums
        Route::get('/subscriptions', [ForumController::class, 'subscriptions'])->name('forum_subscriptions');
        Route::get('/latest/topics', [ForumController::class, 'latestTopics'])->name('forum_latest_topics');
        Route::get('/latest/posts', [ForumController::class, 'latestPosts'])->name('forum_latest_posts');
        Route::get('/search', [ForumController::class, 'search'])->name('forum_search_form');

        Route::group(['prefix' => 'topics'], function () {
            // Create New Topic
            Route::get('/forum/{id}/new-topic', [TopicController::class, 'addForm'])->name('forum_new_topic_form');
            Route::post('/forum/{id}/new-topic', [TopicController::class, 'newTopic'])->name('forum_new_topic');
            // View Topic
            Route::get('/{id}{page?}{post?}', [TopicController::class, 'topic'])->name('forum_topic');
            // Close Topic
            Route::get('/{id}/close', [TopicController::class, 'closeTopic'])->name('forum_close');
            // Open Topic
            Route::get('/{id}/open', [TopicController::class, 'openTopic'])->name('forum_open');
            // Tip Poster
            Route::post('/posts/tip_poster', [BonusController::class, 'tipPoster'])->name('tip_poster');

            // Edit Topic
            Route::get('/{id}/edit', [TopicController::class, 'editForm'])->name('forum_edit_topic_form');
            Route::post('/{id}/edit', [TopicController::class, 'editTopic'])->name('forum_edit_topic');
            // Delete Topic
            Route::get('/{id}/delete', [TopicController::class, 'deleteTopic'])->name('forum_delete_topic');
            // Pin Topic
            Route::get('/{id}/pin', [TopicController::class, 'pinTopic'])->name('forum_pin_topic');
            // Unpin Topic
            Route::get('/{id}/unpin', [TopicController::class, 'unpinTopic'])->name('forum_unpin_topic');
        });

        // Topic Label System
        Route::group(['prefix' => 'topics', 'middleware' => 'modo'], function () {
            Route::name('topics.')->group(function () {
                Route::get('/{id}/approve', [TopicLabelController::class, 'approve'])->name('approve');
                Route::get('/{id}/deny', [TopicLabelController::class, 'deny'])->name('deny');
                Route::get('/{id}/solve', [TopicLabelController::class, 'solve'])->name('solve');
                Route::get('/{id}/invalid', [TopicLabelController::class, 'invalid'])->name('invalid');
                Route::get('/{id}/bug', [TopicLabelController::class, 'bug'])->name('bug');
                Route::get('/{id}/suggest', [TopicLabelController::class, 'suggest'])->name('suggest');
                Route::get('/{id}/implement', [TopicLabelController::class, 'implement'])->name('implement');
            });
        });

        // Subscription System
        Route::get('/subscribe/topic/{route}.{topic}', [SubscriptionController::class, 'subscribeTopic'])->name('subscribe_topic');
        Route::get('/unsubscribe/topic/{route}.{topic}', [SubscriptionController::class, 'unsubscribeTopic'])->name('unsubscribe_topic');
        Route::get('/subscribe/forum/{route}.{forum}', [SubscriptionController::class, 'subscribeForum'])->name('subscribe_forum');
        Route::get('/unsubscribe/forum/{route}.{forum}', [SubscriptionController::class, 'unsubscribeForum'])->name('unsubscribe_forum');
    });

    /*
    |---------------------------------------------------------------------------------
    | Staff Dashboard Routes Group (When Authorized And A Staff Group) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'twostep', 'modo', 'banned']], function () {

        // Staff Dashboard
        Route::name('staff.dashboard.')->group(function () {
            Route::get('/', [HomeController::class, 'index'])->name('index');
        });

        // Articles System
        Route::group(['prefix' => 'articles'], function () {
            Route::name('staff.articles.')->group(function () {
                Route::get('/', [ArticleController::class, 'index'])->name('index');
                Route::get('/create', [ArticleController::class, 'create'])->name('create');
                Route::post('/store', [ArticleController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ArticleController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [ArticleController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [ArticleController::class, 'destroy'])->name('destroy');
            });
        });

        // Applications System
        Route::group(['prefix' => 'applications'], function () {
            Route::name('staff.applications.')->group(function () {
                Route::get('/', [ApplicationController::class, 'index'])->name('index');
                Route::get('/{id}', [ApplicationController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::post('/{id}/approve', [ApplicationController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [ApplicationController::class, 'reject'])->name('reject');
            });
        });

        // Audit Log
        Route::group(['prefix' => 'audits'], function () {
            Route::name('staff.audits.')->group(function () {
                Route::get('/', [AuditController::class, 'index'])->name('index');
                Route::delete('/{id}/destroy', [AuditController::class, 'destroy'])->name('destroy');
            });
        });

        // Authentications Log
        Route::group(['prefix' => 'authentications'], function () {
            Route::name('staff.authentications.')->group(function () {
                Route::get('/', [AuthenticationController::class, 'index'])->name('index');
            });
        });

        // Backup System
        Route::group(['prefix' => 'backups'], function () {
            Route::name('staff.backups.')->group(function () {
                Route::get('/', [BackupController::class, 'index'])->name('index');
            });
        });

        // Ban System
        Route::group(['prefix' => 'bans'], function () {
            Route::name('staff.bans.')->group(function () {
                Route::get('/', [BanController::class, 'index'])->name('index');
                Route::post('/{username}/store', [BanController::class, 'store'])->name('store');
                Route::post('/{username}/update', [BanController::class, 'update'])->name('update');
            });
        });

        // Categories System
        Route::group(['prefix' => 'categories'], function () {
            Route::name('staff.categories.')->group(function () {
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::get('/create', [CategoryController::class, 'create'])->name('create');
                Route::post('/store', [CategoryController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [CategoryController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
            });
        });

        // Chat Bots System
        Route::group(['prefix' => 'chat'], function () {
            Route::name('staff.bots.')->group(function () {
                Route::get('/bots', [ChatBotController::class, 'index'])->name('index');
                Route::get('/bots/{id}/edit', [ChatBotController::class, 'edit'])->name('edit');
                Route::patch('/bots/{id}/update', [ChatBotController::class, 'update'])->name('update');
                Route::delete('/bots/{id}/destroy', [ChatBotController::class, 'destroy'])->name('destroy');
                Route::get('/bots/{id}/disable', [ChatBotController::class, 'disable'])->name('disable');
                Route::get('/bots/{id}/enable', [ChatBotController::class, 'enable'])->name('enable');
            });
        });

        // Chat Rooms System
        Route::group(['prefix' => 'chat'], function () {
            Route::name('staff.rooms.')->group(function () {
                Route::get('/rooms', [ChatRoomController::class, 'index'])->name('index');
                Route::post('/rooms/store', [ChatRoomController::class, 'store'])->name('store');
                Route::post('/rooms/{id}/update', [ChatRoomController::class, 'update'])->name('update');
                Route::delete('/rooms/{id}/destroy', [ChatRoomController::class, 'destroy'])->name('destroy');
            });
        });

        // Chat Statuses System
        Route::group(['prefix' => 'chat'], function () {
            Route::name('staff.statuses.')->group(function () {
                Route::get('/statuses', [ChatStatusController::class, 'index'])->name('index');
                Route::post('/statuses/store', [ChatStatusController::class, 'store'])->name('store');
                Route::post('/statuses/{id}/update', [ChatStatusController::class, 'update'])->name('update');
                Route::delete('/statuses/{id}/destroy', [ChatStatusController::class, 'destroy'])->name('destroy');
            });
        });

        // Cheaters
        Route::group(['prefix' => 'cheaters'], function () {
            Route::name('staff.cheaters.')->group(function () {
                Route::get('/ghost-leechers', [CheaterController::class, 'index'])->name('index');
            });
        });

        // Codebase Version Check
        Route::group(['prefix' => 'UNIT3D'], function () {
            Route::get('/', [VersionController::class, 'checkVersion']);
        });

        // Commands
        Route::group(['prefix' => 'commands'], function () {
            Route::get('/', [CommandController::class, 'index'])->name('staff.commands.index');
            Route::get('/maintance-enable', [CommandController::class, 'maintanceEnable']);
            Route::get('/maintance-disable', [CommandController::class, 'maintanceDisable']);
            Route::get('/clear-cache', [CommandController::class, 'clearCache']);
            Route::get('/clear-view-cache', [CommandController::class, 'clearView']);
            Route::get('/clear-route-cache', [CommandController::class, 'clearRoute']);
            Route::get('/clear-config-cache', [CommandController::class, 'clearConfig']);
            Route::get('/clear-all-cache', [CommandController::class, 'clearAllCache']);
            Route::get('/set-all-cache', [CommandController::class, 'setAllCache']);
            Route::get('/clear-compiled', [CommandController::class, 'clearCompiled']);
            Route::get('/test-email', [CommandController::class, 'testEmail']);
        });

        // Flush System
        Route::group(['prefix' => 'flush'], function () {
            Route::name('staff.flush.')->group(function () {
                Route::get('/peers', [FlushController::class, 'peers'])->name('peers');
                Route::get('/chat', [FlushController::class, 'chat'])->name('chat');
            });
        });

        // Forums System
        Route::group(['prefix' => 'forums'], function () {
            Route::name('staff.forums.')->group(function () {
                Route::get('/', [ForumController::class, 'index'])->name('index');
                Route::get('/create', [ForumController::class, 'create'])->name('create');
                Route::post('/store', [ForumController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ForumController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [ForumController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [ForumController::class, 'destroy'])->name('destroy');
            });
        });

        // Groups System
        Route::group(['prefix' => 'groups'], function () {
            Route::name('staff.groups.')->group(function () {
                Route::get('/', [GroupController::class, 'index'])->name('index');
                Route::get('/create', [GroupController::class, 'create'])->name('create');
                Route::post('/store', [GroupController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [GroupController::class, 'update'])->name('update');
            });
        });

        // Invites Log
        Route::group(['prefix' => 'invites'], function () {
            Route::name('staff.invites.')->group(function () {
                Route::get('/', [InviteController::class, 'index'])->name('index');
            });
        });

        // Mass Actions
        Route::group(['prefix' => 'mass-actions'], function () {
            Route::get('/validate-users', [MassActionController::class, 'update'])->name('staff.mass-actions.validate');
            Route::get('/mass-pm', [MassActionController::class, 'create'])->name('staff.mass-pm.create');
            Route::post('/mass-pm/store', [MassActionController::class, 'store'])->name('staff.mass-pm.store');
        });

        // Media Lanuages (Languages Used To Populate Language Dropdowns For Subtitles / Audios / Etc.)
        Route::group(['prefix' => 'media-languages'], function () {
            Route::name('staff.media_languages.')->group(function () {
                Route::get('/', [MediaLanguageController::class, 'index'])->name('index');
                Route::get('/create', [MediaLanguageController::class, 'create'])->name('create');
                Route::post('/store', [MediaLanguageController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [MediaLanguageController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [MediaLanguageController::class, 'update'])->name('update');
                Route::delete('/{id}/delete', [MediaLanguageController::class, 'destroy'])->name('destroy');
            });
        });

        // Moderation System
        Route::group(['prefix' => 'moderation'], function () {
            Route::name('staff.moderation.')->group(function () {
                Route::get('/', [ModerationController::class, 'index'])->name('index');
                Route::get('/{id}/approve', [ModerationController::class, 'approve'])->name('approve');
                Route::post('/reject', [ModerationController::class, 'reject'])->name('reject');
                Route::post('/postpone', [ModerationController::class, 'postpone'])->name('postpone');
            });
        });

        //Pages System
        Route::group(['prefix' => 'pages'], function () {
            Route::name('staff.pages.')->group(function () {
                Route::get('/', [PageController::class, 'index'])->name('index');
                Route::get('/create', [PageController::class, 'create'])->name('create');
                Route::post('/store', [PageController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [PageController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [PageController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [PageController::class, 'destroy'])->name('destroy');
            });
        });

        // Polls System
        Route::group(['prefix' => 'polls'], function () {
            Route::name('staff.polls.')->group(function () {
                Route::get('/', [PollController::class, 'index'])->name('index');
                Route::get('/{id}', [PollController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/create', [PollController::class, 'create'])->name('create');
                Route::post('/store', [PollController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [PollController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [PollController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [PollController::class, 'destroy'])->name('destroy');
            });
        });

        // Registered Seedboxes
        Route::group(['prefix' => 'seedboxes'], function () {
            Route::name('staff.seedboxes.')->group(function () {
                Route::get('/', [SeedboxController::class, 'index'])->name('index');
                Route::delete('/{id}/destroy', [SeedboxController::class, 'destroy'])->name('destroy');
            });
        });

        // Reports
        Route::group(['prefix' => 'reports'], function () {
            Route::name('staff.reports.')->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::get('/{id}', [ReportController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::post('/{id}/solve', [ReportController::class, 'update'])->name('update');
            });
        });

        // Resolutions
        Route::group(['prefix' => 'resolutions'], function () {
            Route::name('staff.resolutions.')->group(function () {
                Route::get('/', [ResolutionController::class, 'index'])->name('index');
                Route::get('/create', [ResolutionController::class, 'create'])->name('create');
                Route::post('/store', [ResolutionController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ResolutionController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [ResolutionController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [ResolutionController::class, 'destroy'])->name('destroy');
            });
        });

        // RSS System
        Route::group(['prefix' => 'rss'], function () {
            Route::name('staff.rss.')->group(function () {
                Route::get('/', [RssController::class, 'index'])->name('index');
                Route::get('/create', [RssController::class, 'create'])->name('create');
                Route::post('/store', [RssController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [RssController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [RssController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [RssController::class, 'destroy'])->name('destroy');
            });
        });

        // Types
        Route::group(['prefix' => 'types'], function () {
            Route::name('staff.types.')->group(function () {
                Route::get('/', [TypeController::class, 'index'])->name('index');
                Route::get('/create', [TypeController::class, 'create'])->name('create');
                Route::post('/store', [TypeController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [TypeController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [TypeController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [TypeController::class, 'destroy'])->name('destroy');
            });
        });

        // User Gifting (From System)
        Route::group(['prefix' => 'gifts'], function () {
            Route::name('staff.gifts.')->group(function () {
                Route::get('/', [GiftController::class, 'index'])->name('index');
                Route::post('/store', [GiftController::class, 'store'])->name('store');
            });
        });

        // User Staff Notes
        Route::group(['prefix' => 'notes'], function () {
            Route::name('staff.notes.')->group(function () {
                Route::get('/', [NoteController::class, 'index'])->name('index');
                Route::post('/{username}/store', [NoteController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [NoteController::class, 'destroy'])->name('destroy');
            });
        });

        // User Tools TODO: Leaving since we will be refactoring users and roles
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'index'])->name('user_search');
            Route::post('/{username}/edit', [UserController::class, 'edit'])->name('user_edit');
            Route::get('/{username}/settings', [UserController::class, 'settings'])->name('user_setting');
            Route::post('/{username}/permissions', [UserController::class, 'permissions'])->name('user_permissions');
            Route::post('/{username}/password', [UserController::class, 'password'])->name('user_password');
            Route::get('/{username}/destroy', [UserController::class, 'destroy'])->name('user_delete');
        });

        // Warnings Log
        Route::group(['prefix' => 'warnings'], function () {
            Route::name('staff.warnings.')->group(function () {
                Route::get('/', [WarningController::class, 'index'])->name('index');
            });
        });

        // Watchlist
        Route::group(['prefix' => 'watchlist'], function () {
            Route::name('staff.watchlist.')->group(function () {
                Route::get('/', [WatchlistController::class, 'index'])->name('index');
                Route::post('/{id}/store', [WatchlistController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [WatchlistController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
