<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

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
| Here is where you can register all the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
if (config('unit3d.proxy_scheme')) {
    URL::forceScheme(config('unit3d.proxy_scheme'));
}
if (config('unit3d.root_url_override')) {
    URL::forceRootUrl(config('unit3d.root_url_override'));
}
Route::middleware('language')->group(function (): void {
    /*
    |---------------------------------------------------------------------------------
    | Website (Not Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::middleware('guest')->group(function (): void {
        // Activation
        Route::get('/activate/{token}', [App\Http\Controllers\Auth\ActivationController::class, 'activate'])->name('activate');

        // Application Signup
        Route::get('/application', [App\Http\Controllers\Auth\ApplicationController::class, 'create'])->name('application.create');
        Route::post('/application', [App\Http\Controllers\Auth\ApplicationController::class, 'store'])->name('application.store');

        // Authentication
        Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('');

        // Forgot Username
        Route::get('username/reminder', [App\Http\Controllers\Auth\ForgotUsernameController::class, 'showForgotUsernameForm'])->name('username.request');
        Route::post('username/reminder', [App\Http\Controllers\Auth\ForgotUsernameController::class, 'sendUsernameReminder'])->name('username.email');

        // Password Reset
        Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('');
        Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');

        // Registration
        Route::get('/register/{code?}', [App\Http\Controllers\Auth\RegisterController::class, 'registrationForm'])->name('registrationForm');
        Route::post('/register/{code?}', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
    });

    /*
    |---------------------------------------------------------------------------------
    | Website (When Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'twostep', 'banned'])->group(function (): void {
        // General
        Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home.index');

        // Articles System
        Route::prefix('articles')->group(function (): void {
            Route::name('articles.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\ArticleController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\ArticleController::class, 'show'])->name('show');
            });
        });

        // RSS System
        Route::prefix('rss')->group(function (): void {
            Route::name('rss.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\RssController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\RssController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\RssController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\RssController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\RssController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\RssController::class, 'destroy'])->name('destroy');
            });
        });

        // TwoStep Auth System
        Route::prefix('twostep')->group(function (): void {
            Route::get('/needed', [App\Http\Controllers\Auth\TwoStepController::class, 'showVerification'])->name('verificationNeeded');
            Route::post('/verify', [App\Http\Controllers\Auth\TwoStepController::class, 'verify'])->name('verify');
            Route::post('/resend', [App\Http\Controllers\Auth\TwoStepController::class, 'resend'])->name('resend');
        });

        // Reports System
        Route::prefix('reports')->group(function (): void {
            Route::post('/torrent/{id}', [App\Http\Controllers\ReportController::class, 'torrent'])->name('report_torrent');
            Route::post('/request/{id}', [App\Http\Controllers\ReportController::class, 'request'])->name('report_request');
            Route::post('/user/{username}', [App\Http\Controllers\ReportController::class, 'user'])->name('report_user');
        });

        // Contact Us System
        Route::prefix('contact')->group(function (): void {
            Route::name('contact.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\ContactController::class, 'index'])->name('index');
                Route::post('/store', [App\Http\Controllers\ContactController::class, 'store'])->name('store');
            });
        });

        // Pages System
        Route::prefix('pages')->group(function (): void {
            Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('pages.index');
            Route::get('/staff', [App\Http\Controllers\PageController::class, 'staff'])->name('staff');
            Route::get('/internal', [App\Http\Controllers\PageController::class, 'internal'])->name('internal');
            Route::get('/blacklist/clients', [App\Http\Controllers\PageController::class, 'clientblacklist'])->name('client_blacklist');
            Route::get('/aboutus', [App\Http\Controllers\PageController::class, 'about'])->name('about');
            Route::get('/{id}', [App\Http\Controllers\PageController::class, 'show'])->where('id', '[0-9]+')->name('pages.show');
        });

        // Extra-Stats System
        Route::prefix('stats')->group(function (): void {
            Route::get('/', [App\Http\Controllers\StatsController::class, 'index'])->name('stats');
            Route::get('/user/clients', [App\Http\Controllers\StatsController::class, 'clients'])->name('clients');
            Route::get('/user/uploaded', [App\Http\Controllers\StatsController::class, 'uploaded'])->name('uploaded');
            Route::get('/user/downloaded', [App\Http\Controllers\StatsController::class, 'downloaded'])->name('downloaded');
            Route::get('/user/seeders', [App\Http\Controllers\StatsController::class, 'seeders'])->name('seeders');
            Route::get('/user/leechers', [App\Http\Controllers\StatsController::class, 'leechers'])->name('leechers');
            Route::get('/user/uploaders', [App\Http\Controllers\StatsController::class, 'uploaders'])->name('uploaders');
            Route::get('/user/bankers', [App\Http\Controllers\StatsController::class, 'bankers'])->name('bankers');
            Route::get('/user/seedtime', [App\Http\Controllers\StatsController::class, 'seedtime'])->name('seedtime');
            Route::get('/user/seedsize', [App\Http\Controllers\StatsController::class, 'seedsize'])->name('seedsize');
            Route::get('/torrent/seeded', [App\Http\Controllers\StatsController::class, 'seeded'])->name('seeded');
            Route::get('/torrent/leeched', [App\Http\Controllers\StatsController::class, 'leeched'])->name('leeched');
            Route::get('/torrent/completed', [App\Http\Controllers\StatsController::class, 'completed'])->name('completed');
            Route::get('/torrent/dying', [App\Http\Controllers\StatsController::class, 'dying'])->name('dying');
            Route::get('/torrent/dead', [App\Http\Controllers\StatsController::class, 'dead'])->name('dead');
            Route::get('/request/bountied', [App\Http\Controllers\StatsController::class, 'bountied'])->name('bountied');
            Route::get('/groups', [App\Http\Controllers\StatsController::class, 'groups'])->name('groups');
            Route::get('/groups/group/{id}', [App\Http\Controllers\StatsController::class, 'group'])->name('group');
            Route::get('/languages', [App\Http\Controllers\StatsController::class, 'languages'])->name('languages');
            Route::get('/themes', [App\Http\Controllers\StatsController::class, 'themes'])->name('themes');
        });

        // Requests System
        Route::prefix('requests')->name('requests.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\RequestController::class, 'index'])->name('index');
            Route::get('/add', [App\Http\Controllers\RequestController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\RequestController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\RequestController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [App\Http\Controllers\RequestController::class, 'update'])->name('update');
            Route::get('/{id}', [App\Http\Controllers\RequestController::class, 'show'])->name('show');
            Route::delete('/{id}', [App\Http\Controllers\RequestController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/accept', [App\Http\Controllers\RequestController::class, 'approve'])->name('approve');
            Route::post('/{id}/fill', [App\Http\Controllers\RequestController::class, 'fill'])->name('fill');
            Route::post('/{id}/reject', [App\Http\Controllers\RequestController::class, 'reject'])->name('reject');
            Route::post('/{id}/reset', [App\Http\Controllers\RequestController::class, 'reset'])->name('reset')->middleware('modo');

            Route::prefix('bounties')->name('bounties.')->group(function (): void {
                Route::post('/{id}', [App\Http\Controllers\BountyController::class, 'store'])->name('store');
            });

            Route::prefix('claims')->name('claims.')->group(function (): void {
                Route::post('/{id}', [App\Http\Controllers\ClaimController::class, 'store'])->name('store');
                Route::delete('/{id}', [App\Http\Controllers\ClaimController::class, 'destroy'])->name('destroy');
            });
        });

        // Top 10 System
        Route::prefix('top10')->group(function (): void {
            Route::name('top10.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Top10Controller::class, 'index'])->name('index');
            });
        });

        // Torrents System
        Route::prefix('upload')->group(function (): void {
            Route::get('/', [App\Http\Controllers\TorrentController::class, 'create'])->name('upload_form');
            Route::post('/', [App\Http\Controllers\TorrentController::class, 'store'])->name('upload');
        });

        Route::prefix('torrents')->group(function (): void {
            Route::get('/', [App\Http\Controllers\TorrentController::class, 'index'])->name('torrents');
            Route::get('/{id}{hash?}', [App\Http\Controllers\TorrentController::class, 'show'])->name('torrent');
            Route::get('/{id}/peers', [App\Http\Controllers\TorrentPeerController::class, 'index'])->name('peers');
            Route::get('/{id}/history', [App\Http\Controllers\TorrentHistoryController::class, 'index'])->name('history');
            Route::get('/download_check/{id}', [App\Http\Controllers\TorrentDownloadController::class, 'show'])->name('download_check');
            Route::get('/download/{id}', [App\Http\Controllers\TorrentDownloadController::class, 'store'])->name('download');
            Route::post('/delete', [App\Http\Controllers\TorrentController::class, 'destroy'])->name('delete');
            Route::get('/{id}/edit', [App\Http\Controllers\TorrentController::class, 'edit'])->name('edit_form');
            Route::post('/{id}/edit', [App\Http\Controllers\TorrentController::class, 'update'])->name('edit');
            Route::post('/{id}/reseed', [App\Http\Controllers\ReseedController::class, 'store'])->name('reseed');
            Route::get('/similar/{category_id}.{tmdb}', [App\Http\Controllers\SimilarTorrentController::class, 'show'])->name('torrents.similar');
        });

        Route::prefix('torrent')->group(function (): void {
            Route::post('/{id}/torrent_fl', [App\Http\Controllers\TorrentBuffController::class, 'grantFL'])->name('torrent_fl');
            Route::post('/{id}/torrent_doubleup', [App\Http\Controllers\TorrentBuffController::class, 'grantDoubleUp'])->name('torrent_doubleup');
            Route::post('/{id}/bumpTorrent', [App\Http\Controllers\TorrentBuffController::class, 'bumpTorrent'])->name('bumpTorrent');
            Route::post('/{id}/torrent_sticky', [App\Http\Controllers\TorrentBuffController::class, 'sticky'])->name('torrent_sticky');
            Route::post('/{id}/torrent_feature', [App\Http\Controllers\TorrentBuffController::class, 'grantFeatured'])->name('torrent_feature');
            Route::post('/{id}/torrent_revokefeature', [App\Http\Controllers\TorrentBuffController::class, 'revokeFeatured'])->name('torrent_revokefeature');
            Route::post('/{id}/freeleech_token', [App\Http\Controllers\TorrentBuffController::class, 'freeleechToken'])->name('freeleech_token');
            Route::post('/{id}/refundable', [App\Http\Controllers\TorrentBuffController::class, 'setRefundable'])->name('refundable');
        });

        // Poll System
        Route::prefix('polls')->group(function (): void {
            Route::get('/', [App\Http\Controllers\PollController::class, 'index'])->name('polls');
            Route::post('/vote', [App\Http\Controllers\PollController::class, 'vote']);
            Route::get('/{id}', [App\Http\Controllers\PollController::class, 'show'])->where('id', '[0-9]+')->name('poll');
            Route::get('/{id}/result', [App\Http\Controllers\PollController::class, 'result'])->name('poll_results');
        });

        // Graveyard System
        Route::prefix('graveyard')->group(function (): void {
            Route::name('graveyard.')->group(function (): void {
                Route::post('/', [App\Http\Controllers\GraveyardController::class, 'store'])->name('store');
                Route::delete('/{id}', [App\Http\Controllers\GraveyardController::class, 'destroy'])->name('destroy');
            });
        });

        // Playlist System
        Route::prefix('playlists')->group(function (): void {
            Route::name('playlists.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\PlaylistController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\PlaylistController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\PlaylistController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\PlaylistController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\PlaylistController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\PlaylistController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\PlaylistController::class, 'destroy'])->name('destroy');
                Route::post('/attach', [App\Http\Controllers\PlaylistTorrentController::class, 'store'])->name('attach');
                Route::delete('/{id}/detach', [App\Http\Controllers\PlaylistTorrentController::class, 'destroy'])->name('detach');
                Route::get('/{id}/download', [App\Http\Controllers\PlaylistController::class, 'downloadPlaylist'])->name('download');
            });
        });

        // Subtitles System
        Route::prefix('subtitles')->group(function (): void {
            Route::name('subtitles.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\SubtitleController::class, 'index'])->name('index');
                Route::get('/create/{torrent_id}', [App\Http\Controllers\SubtitleController::class, 'create'])->where('id', '[0-9]+')->name('create');
                Route::post('/store', [App\Http\Controllers\SubtitleController::class, 'store'])->name('store');
                Route::post('/{id}/update', [App\Http\Controllers\SubtitleController::class, 'update'])->name('update');
                Route::delete('/{id}/delete', [App\Http\Controllers\SubtitleController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/download', [App\Http\Controllers\SubtitleController::class, 'download'])->name('download');
            });
        });

        // Tickets System
        Route::prefix('tickets')->group(function (): void {
            Route::name('tickets.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\TicketController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\TicketController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\TicketController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\TicketController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\TicketController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\TicketController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\TicketController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/assign', [App\Http\Controllers\TicketController::class, 'assign'])->name('assign');
                Route::post('/{id}/unassign', [App\Http\Controllers\TicketController::class, 'unassign'])->name('unassign');
                Route::post('/{id}/close', [App\Http\Controllers\TicketController::class, 'close'])->name('close');
                Route::post('/attachments/{attachment}/download', [App\Http\Controllers\TicketAttachmentController::class, 'download'])->name('attachment.download');
            });
        });

        // Missing System
        Route::prefix('missing')->group(function (): void {
            Route::name('missing.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\MissingController::class, 'index'])->name('index');
            });
        });
    });

    /*
    |------------------------------------------
    | MediaHub (When Authorized)
    |------------------------------------------
    */
    Route::prefix('mediahub')->middleware(['auth', 'twostep', 'banned'])->group(function (): void {
        // MediaHub Home
        Route::get('/', [App\Http\Controllers\MediaHub\HomeController::class, 'index'])->name('mediahub.index');

        // Genres
        Route::get('/genres', [App\Http\Controllers\MediaHub\GenreController::class, 'index'])->name('mediahub.genres.index');

        // Networks
        Route::get('/networks', [App\Http\Controllers\MediaHub\NetworkController::class, 'index'])->name('mediahub.networks.index');

        // Companies
        Route::get('/companies', [App\Http\Controllers\MediaHub\CompanyController::class, 'index'])->name('mediahub.companies.index');

        // TV Shows
        Route::get('/tv-shows', [App\Http\Controllers\MediaHub\TvShowController::class, 'index'])->name('mediahub.shows.index');

        // TV Show
        Route::get('/tv-show/{id}', [App\Http\Controllers\MediaHub\TvShowController::class, 'show'])->name('mediahub.shows.show');

        // TV Show Season
        Route::get('/tv-show/season/{id}', [App\Http\Controllers\MediaHub\TvSeasonController::class, 'show'])->name('mediahub.season.show');

        // Persons
        Route::get('/persons', [App\Http\Controllers\MediaHub\PersonController::class, 'index'])->name('mediahub.persons.index');

        // Person
        Route::get('/persons/{id}', [App\Http\Controllers\MediaHub\PersonController::class, 'show'])->name('mediahub.persons.show');

        // Collections
        Route::get('/collections', [App\Http\Controllers\MediaHub\CollectionController::class, 'index'])->name('mediahub.collections.index');

        // Collection
        Route::get('/collections/{id}', [App\Http\Controllers\MediaHub\CollectionController::class, 'show'])->name('mediahub.collections.show');
    });

    /*
    |---------------------------------------------------------------------------------
    | Forums Routes Group (When Authorized) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::prefix('forums')->middleware(['auth', 'twostep', 'banned'])->group(function (): void {
        // Forum System
        Route::name('forums.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\ForumController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\ForumController::class, 'show'])->where('id', '[0-9]+')->name('show');
        });

        // Forum Category System
        Route::prefix('categories')->name('forums.categories.')->group(function (): void {
            Route::get('/{id}', [App\Http\Controllers\ForumCategoryController::class, 'show'])->where('id', '[0-9]+')->name('show');
        });

        // Posts System
        Route::prefix('posts')->name('posts.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\PostController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\PostController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\PostController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('destroy');
        });

        //Topics System
        Route::prefix('topics')->name('topics.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\TopicController::class, 'index'])->name('index');
            Route::get('/forum/{id}/create', [App\Http\Controllers\TopicController::class, 'create'])->name('create');
            Route::post('/forum/{id}', [App\Http\Controllers\TopicController::class, 'store'])->name('store');
            Route::get('/{id}{page?}{post?}', [App\Http\Controllers\TopicController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\TopicController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [App\Http\Controllers\TopicController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\TopicController::class, 'destroy'])->name('destroy')->middleware('modo');
            Route::post('/{id}/close', [App\Http\Controllers\TopicController::class, 'close'])->name('close')->middleware('modo');
            Route::post('/{id}/open', [App\Http\Controllers\TopicController::class, 'open'])->name('open')->middleware('modo');
            Route::post('/{id}/pin', [App\Http\Controllers\TopicController::class, 'pin'])->name('pin')->middleware('modo');
            Route::post('/{id}/unpin', [App\Http\Controllers\TopicController::class, 'unpin'])->name('unpin')->middleware('modo');
        });

        // Topic Label System
        Route::prefix('topics')->name('topics.')->middleware('modo')->group(function (): void {
            Route::post('/{id}/approve', [App\Http\Controllers\TopicLabelController::class, 'approve'])->name('approve');
            Route::post('/{id}/deny', [App\Http\Controllers\TopicLabelController::class, 'deny'])->name('deny');
            Route::post('/{id}/solve', [App\Http\Controllers\TopicLabelController::class, 'solve'])->name('solve');
            Route::post('/{id}/invalid', [App\Http\Controllers\TopicLabelController::class, 'invalid'])->name('invalid');
            Route::post('/{id}/bug', [App\Http\Controllers\TopicLabelController::class, 'bug'])->name('bug');
            Route::post('/{id}/suggest', [App\Http\Controllers\TopicLabelController::class, 'suggest'])->name('suggest');
            Route::post('/{id}/implement', [App\Http\Controllers\TopicLabelController::class, 'implement'])->name('implement');
        });

        // Subscription System
        Route::prefix('subscriptions')->name('subscriptions.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\SubscriptionController::class, 'store'])->name('store');
            Route::post('/{id}', [App\Http\Controllers\SubscriptionController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |-------------------------------------------------------------------------------
    | User Private Routes Group (When authorized) (Alpha ordered)
    |-------------------------------------------------------------------------------
    */
    Route::prefix('users/{user:username}')->name('users.')->middleware(['auth', 'twostep', 'banned'])->group(function (): void {
        // Achievements
        Route::prefix('achievements')->name('achievements.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\AchievementsController::class, 'index'])->name('index');
        });

        // Bans
        Route::prefix('bans')->name('bans.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\BanController::class, 'index'])->name('index');
        });

        // History
        Route::prefix('torrents')->name('history.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\HistoryController::class, 'index'])->name('index');
        });

        // Followers
        Route::prefix('followers')->name('followers.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\FollowController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\User\FollowController::class, 'store'])->name('store');
            Route::delete('/', [App\Http\Controllers\User\FollowController::class, 'destroy'])->name('destroy');
        });

        // Following
        Route::prefix('following')->name('following.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\FollowingController::class, 'index'])->name('index');
        });

        // General settings
        Route::prefix('general-settings')->name('general_settings.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\GeneralSettingController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\GeneralSettingController::class, 'update'])->name('update');
        });

        // Privacy settings
        Route::prefix('privacy-settings')->name('privacy_settings.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\PrivacySettingController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\PrivacySettingController::class, 'update'])->name('update');
        });

        // Notification settings
        Route::prefix('notification-settings')->name('notification_settings.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\NotificationSettingController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\NotificationSettingController::class, 'update'])->name('update');
        });

        // Peers
        Route::prefix('active')->name('peers.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\PeerController::class, 'index'])->name('index');
            Route::delete('/', [App\Http\Controllers\User\PeerController::class, 'massDestroy'])->name('mass_destroy');
        });

        // Posts
        Route::prefix('posts')->name('posts.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\PostController::class, 'index'])->name('index');
        });

        // Resurrections
        Route::prefix('resurrections')->name('resurrections.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\ResurrectionController::class, 'index'])->name('index');
        });

        // Email
        Route::prefix('email')->name('email.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\EmailController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\EmailController::class, 'update'])->name('update');
        });

        // Password
        Route::prefix('password')->name('password.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\PasswordController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\PasswordController::class, 'update'])->name('update');
        });

        // Passkey
        Route::prefix('passkey')->name('passkey.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\PasskeyController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\PasskeyController::class, 'update'])->name('update');
        });

        // Rsskey
        Route::prefix('rsskey')->name('rsskey.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\RsskeyController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\RsskeyController::class, 'update'])->name('update');
        });

        // Apikey
        Route::prefix('apikey')->name('apikey.')->group(function (): void {
            Route::get('/edit', [App\Http\Controllers\User\ApikeyController::class, 'edit'])->name('edit');
            Route::patch('/', [App\Http\Controllers\User\ApikeyController::class, 'update'])->name('update');
        });

        // Two-Step Authentication
        if (config('auth.TwoStepEnabled') === true) {
            Route::prefix('two-step')->name('two_step.')->group(function (): void {
                Route::get('/edit', [App\Http\Controllers\User\TwoStepController::class, 'edit'])->name('edit');
                Route::patch('/', [App\Http\Controllers\User\TwoStepController::class, 'update'])->name('update');
            });
        }

        // Topics
        Route::prefix('topics')->name('topics.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\TopicController::class, 'index'])->name('index');
        });

        // Torrent Zip
        Route::prefix('torrent-zip')->name('torrent_zip.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\TorrentZipController::class, 'show'])->name('show');
        });

        // Torrents
        Route::prefix('uploads')->name('torrents.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\TorrentController::class, 'index'])->name('index');
        });
    });

    Route::middleware('auth', 'twostep', 'banned')->group(function (): void {
        // Earnings
        Route::prefix('users/{username}/earnings')->name('earnings.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\EarningController::class, 'index'])->name('index');
        });

        // Gifts
        Route::prefix('users/{username}/gifts')->name('gifts.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\GiftController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\User\GiftController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\User\GiftController::class, 'store'])->name('store');
        });

        // Invites
        Route::prefix('invites')->name('invites.')->group(function (): void {
            Route::get('/create', [App\Http\Controllers\User\InviteController::class, 'create'])->name('create');
            Route::post('/store', [App\Http\Controllers\User\InviteController::class, 'store'])->name('store');
            Route::post('/{id}/send', [App\Http\Controllers\User\InviteController::class, 'send'])->where('id', '[0-9]+')->name('send');
            Route::delete('/{id}', [App\Http\Controllers\User\InviteController::class, 'destroy'])->name('destroy');
            Route::get('/{username}', [App\Http\Controllers\User\InviteController::class, 'index'])->name('index');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/filter', [App\Http\Controllers\User\NotificationController::class, 'faceted']);
            Route::get('/', [App\Http\Controllers\User\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/update', [App\Http\Controllers\User\NotificationController::class, 'update'])->name('update');
            Route::post('/updateall', [App\Http\Controllers\User\NotificationController::class, 'updateAll'])->name('updateall');
            Route::delete('/{id}/destroy', [App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/destroyall', [App\Http\Controllers\User\NotificationController::class, 'destroyAll'])->name('destroyall');
            Route::get('/{id}', [App\Http\Controllers\User\NotificationController::class, 'show'])->name('show');
        });

        // Private Messages
        Route::prefix('mail')->group(function (): void {
            Route::get('/searchPMInbox', [App\Http\Controllers\User\PrivateMessageController::class, 'searchPMInbox'])->name('searchPMInbox');
            Route::get('/searchPMOutbox', [App\Http\Controllers\User\PrivateMessageController::class, 'searchPMOutbox'])->name('searchPMOutbox');
            Route::get('/inbox', [App\Http\Controllers\User\PrivateMessageController::class, 'getPrivateMessages'])->name('inbox');
            Route::get('/message/{id}', [App\Http\Controllers\User\PrivateMessageController::class, 'getPrivateMessageById'])->name('message');
            Route::get('/outbox', [App\Http\Controllers\User\PrivateMessageController::class, 'getPrivateMessagesSent'])->name('outbox');
            Route::get('/create', [App\Http\Controllers\User\PrivateMessageController::class, 'makePrivateMessage'])->name('create');
            Route::post('/mark-all-read', [App\Http\Controllers\User\PrivateMessageController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/empty-inbox', [App\Http\Controllers\User\PrivateMessageController::class, 'emptyInbox'])->name('empty-inbox');
            Route::post('/send', [App\Http\Controllers\User\PrivateMessageController::class, 'sendPrivateMessage'])->name('send-pm');
            Route::post('/{id}/reply', [App\Http\Controllers\User\PrivateMessageController::class, 'replyPrivateMessage'])->name('reply-pm');
            Route::post('/{id}/destroy', [App\Http\Controllers\User\PrivateMessageController::class, 'deletePrivateMessage'])->name('delete-pm');
        });

        // Profile
        Route::prefix('users')->group(function (): void {
            Route::get('/{username}', [App\Http\Controllers\User\UserController::class, 'show'])->name('users.show');
            Route::get('/{username}/edit', [App\Http\Controllers\User\UserController::class, 'editProfileForm'])->name('user_edit_profile_form');
            Route::post('/{username}/edit', [App\Http\Controllers\User\UserController::class, 'editProfile'])->name('user_edit_profile');
        });

        // Rules
        Route::prefix('users')->group(function (): void {
            Route::post('/accept-rules', [App\Http\Controllers\User\UserController::class, 'acceptRules'])->name('accept.rules');
        });

        // Seedboxes
        Route::prefix('users')->group(function (): void {
            Route::get('/{username}/seedboxes', [App\Http\Controllers\User\SeedboxController::class, 'index'])->name('seedboxes.index');
            Route::post('/{username}/seedboxes', [App\Http\Controllers\User\SeedboxController::class, 'store'])->name('seedboxes.store');
            Route::delete('/seedboxes/{id}', [App\Http\Controllers\User\SeedboxController::class, 'destroy'])->name('seedboxes.destroy');
        });

        // Tips
        Route::prefix('users/{username}/tips')->name('tips.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\User\TipController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\User\TipController::class, 'store'])->name('store');
        });

        // Transactions
        Route::prefix('users/{username}/transactions')->name('transactions.')->group(function (): void {
            Route::get('/create', [App\Http\Controllers\User\TransactionController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\User\TransactionController::class, 'store'])->name('store');
        });

        // Warnings
        Route::prefix('warnings')->group(function (): void {
            Route::post('/{id}/deactivate', [App\Http\Controllers\User\WarningController::class, 'deactivate'])->name('deactivateWarning');
            Route::post('/{username}/mass-deactivate', [App\Http\Controllers\User\WarningController::class, 'deactivateAllWarnings'])->name('massDeactivateWarnings');
            Route::delete('/{id}', [App\Http\Controllers\User\WarningController::class, 'deleteWarning'])->name('deleteWarning');
            Route::delete('/{username}/mass-delete', [App\Http\Controllers\User\WarningController::class, 'deleteAllWarnings'])->name('massDeleteWarnings');
            Route::post('/{id}/restore', [App\Http\Controllers\User\WarningController::class, 'restoreWarning'])->name('restoreWarning');
            Route::get('/{username}', [App\Http\Controllers\User\WarningController::class, 'show'])->name('warnings.show');
        });

        // Wishlist
        Route::prefix('wishes')->name('wishes.')->group(function (): void {
            Route::get('/{username}', [App\Http\Controllers\User\WishController::class, 'index'])->name('index');
            Route::post('/store', [App\Http\Controllers\User\WishController::class, 'store'])->name('store');
            Route::delete('/{id}/destroy', [App\Http\Controllers\User\WishController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |---------------------------------------------------------------------------------
    | Staff Dashboard Routes Group (When Authorized And A Staff Group) (Alpha Ordered)
    |---------------------------------------------------------------------------------
    */
    Route::prefix('dashboard')->middleware(['auth', 'twostep', 'modo', 'banned'])->group(function (): void {
        // Staff Dashboard
        Route::name('staff.dashboard.')->group(function (): void {
            Route::get('/', [App\Http\Controllers\Staff\HomeController::class, 'index'])->name('index');
        });

        // Articles System
        Route::prefix('articles')->group(function (): void {
            Route::name('staff.articles.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ArticleController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\ArticleController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\ArticleController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\ArticleController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\ArticleController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\ArticleController::class, 'destroy'])->name('destroy');
            });
        });

        // Applications System
        Route::prefix('applications')->group(function (): void {
            Route::name('staff.applications.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ApplicationController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\Staff\ApplicationController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::post('/{id}/approve', [App\Http\Controllers\Staff\ApplicationController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [App\Http\Controllers\Staff\ApplicationController::class, 'reject'])->name('reject');
            });
        });

        // Audit Log
        Route::prefix('audits')->group(function (): void {
            Route::name('staff.audits.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\AuditController::class, 'index'])->name('index');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\AuditController::class, 'destroy'])->name('destroy');
            });
        });

        // Authentications Log
        Route::prefix('authentications')->group(function (): void {
            Route::name('staff.authentications.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\AuthenticationController::class, 'index'])->name('index');
            });
        });

        // Backup System
        Route::prefix('backups')->middleware('owner')->group(function (): void {
            Route::name('staff.backups.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\BackupController::class, 'index'])->name('index');
            });
        });

        // Ban System
        Route::prefix('bans')->group(function (): void {
            Route::name('staff.bans.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\BanController::class, 'index'])->name('index');
                Route::post('/{username}/store', [App\Http\Controllers\Staff\BanController::class, 'store'])->name('store');
                Route::post('/{username}/update', [App\Http\Controllers\Staff\BanController::class, 'update'])->name('update');
            });
        });

        // Blacklist System
        Route::prefix('blacklists')->group(function (): void {
            Route::name('staff.blacklists.clients.')->group(function (): void {
                Route::get('/clients', [App\Http\Controllers\Staff\BlacklistClientController::class, 'index'])->name('index');
                Route::get('/clients/create', [App\Http\Controllers\Staff\BlacklistClientController::class, 'create'])->name('create');
                Route::post('/clients/store', [App\Http\Controllers\Staff\BlacklistClientController::class, 'store'])->name('store');
                Route::get('/clients/{id}/edit', [App\Http\Controllers\Staff\BlacklistClientController::class, 'edit'])->name('edit');
                Route::patch('/clients/{id}/update', [App\Http\Controllers\Staff\BlacklistClientController::class, 'update'])->name('update');
                Route::delete('/clients/{id}/destroy', [App\Http\Controllers\Staff\BlacklistClientController::class, 'destroy'])->name('destroy');
            });
        });

        // Bon Exchanges
        Route::prefix('bon-exchanges')->group(function (): void {
            Route::name('staff.bon_exchanges.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\BonExchangeController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\BonExchangeController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Staff\BonExchangeController::class, 'store'])->name('store');
                Route::get('/{bonExchange}/edit', [App\Http\Controllers\Staff\BonExchangeController::class, 'edit'])->name('edit');
                Route::patch('/{bonExchange}', [App\Http\Controllers\Staff\BonExchangeController::class, 'update'])->name('update');
                Route::delete('/{bonExchange}', [App\Http\Controllers\Staff\BonExchangeController::class, 'destroy'])->name('destroy');
            });
        });

        // Categories System
        Route::prefix('categories')->group(function (): void {
            Route::name('staff.categories.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\CategoryController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\CategoryController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\CategoryController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\CategoryController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\CategoryController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\CategoryController::class, 'destroy'])->name('destroy');
            });
        });

        // Chat Bots System
        Route::prefix('chat')->group(function (): void {
            Route::name('staff.bots.')->group(function (): void {
                Route::get('/bots', [App\Http\Controllers\Staff\ChatBotController::class, 'index'])->name('index');
                Route::get('/bots/{id}/edit', [App\Http\Controllers\Staff\ChatBotController::class, 'edit'])->name('edit');
                Route::patch('/bots/{id}/update', [App\Http\Controllers\Staff\ChatBotController::class, 'update'])->name('update');
                Route::delete('/bots/{id}/destroy', [App\Http\Controllers\Staff\ChatBotController::class, 'destroy'])->name('destroy');
                Route::post('/bots/{id}/disable', [App\Http\Controllers\Staff\ChatBotController::class, 'disable'])->name('disable');
                Route::post('/bots/{id}/enable', [App\Http\Controllers\Staff\ChatBotController::class, 'enable'])->name('enable');
            });
        });

        // Chat Rooms System
        Route::prefix('chat')->group(function (): void {
            Route::name('staff.rooms.')->group(function (): void {
                Route::get('/rooms', [App\Http\Controllers\Staff\ChatRoomController::class, 'index'])->name('index');
                Route::get('/rooms/create', [App\Http\Controllers\Staff\ChatRoomController::class, 'create'])->name('create');
                Route::post('/rooms/store', [App\Http\Controllers\Staff\ChatRoomController::class, 'store'])->name('store');
                Route::get('/rooms/{id}/edit', [App\Http\Controllers\Staff\ChatRoomController::class, 'edit'])->name('edit');
                Route::post('/rooms/{id}/update', [App\Http\Controllers\Staff\ChatRoomController::class, 'update'])->name('update');
                Route::delete('/rooms/{id}/destroy', [App\Http\Controllers\Staff\ChatRoomController::class, 'destroy'])->name('destroy');
            });
        });

        // Chat Statuses System
        Route::prefix('chat')->group(function (): void {
            Route::name('staff.statuses.')->group(function (): void {
                Route::get('/statuses', [App\Http\Controllers\Staff\ChatStatusController::class, 'index'])->name('index');
                Route::get('/statuses/create', [App\Http\Controllers\Staff\ChatStatusController::class, 'create'])->name('create');
                Route::post('/statuses/store', [App\Http\Controllers\Staff\ChatStatusController::class, 'store'])->name('store');
                Route::get('/statuses/{id}/edit', [App\Http\Controllers\Staff\ChatStatusController::class, 'edit'])->name('edit');
                Route::post('/statuses/{id}/update', [App\Http\Controllers\Staff\ChatStatusController::class, 'update'])->name('update');
                Route::delete('/statuses/{id}/destroy', [App\Http\Controllers\Staff\ChatStatusController::class, 'destroy'])->name('destroy');
            });
        });

        // Cheated Torrents
        Route::prefix('cheated-torrents')->group(function (): void {
            Route::name('staff.cheated_torrents.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\CheatedTorrentController::class, 'index'])->name('index');
                Route::delete('/{id}', [App\Http\Controllers\Staff\CheatedTorrentController::class, 'destroy'])->name('destroy');
                Route::delete('/', [App\Http\Controllers\Staff\CheatedTorrentController::class, 'massDestroy'])->name('massDestroy');
            });
        });

        // Cheaters
        Route::prefix('cheaters')->group(function (): void {
            Route::name('staff.cheaters.')->group(function (): void {
                Route::get('/ghost-leechers', [App\Http\Controllers\Staff\CheaterController::class, 'index'])->name('index');
            });
        });

        // Codebase Version Check
        Route::prefix('UNIT3D')->group(function (): void {
            Route::get('/', [App\Http\Controllers\Staff\VersionController::class, 'checkVersion']);
        });

        // Commands
        Route::prefix('commands')->middleware('owner')->group(function (): void {
            Route::get('/', [App\Http\Controllers\Staff\CommandController::class, 'index'])->name('staff.commands.index');
            Route::post('/maintance-enable', [App\Http\Controllers\Staff\CommandController::class, 'maintanceEnable']);
            Route::post('/maintance-disable', [App\Http\Controllers\Staff\CommandController::class, 'maintanceDisable']);
            Route::post('/clear-cache', [App\Http\Controllers\Staff\CommandController::class, 'clearCache']);
            Route::post('/clear-view-cache', [App\Http\Controllers\Staff\CommandController::class, 'clearView']);
            Route::post('/clear-route-cache', [App\Http\Controllers\Staff\CommandController::class, 'clearRoute']);
            Route::post('/clear-config-cache', [App\Http\Controllers\Staff\CommandController::class, 'clearConfig']);
            Route::post('/clear-all-cache', [App\Http\Controllers\Staff\CommandController::class, 'clearAllCache']);
            Route::post('/set-all-cache', [App\Http\Controllers\Staff\CommandController::class, 'setAllCache']);
            Route::post('/test-email', [App\Http\Controllers\Staff\CommandController::class, 'testEmail']);
        });

        // Distributors
        Route::prefix('distributors')->group(function (): void {
            Route::name('staff.distributors.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\DistributorController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\DistributorController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\DistributorController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\DistributorController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\DistributorController::class, 'update'])->name('update');
                Route::get('/{id}/delete', [App\Http\Controllers\Staff\DistributorController::class, 'delete'])->name('delete');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\DistributorController::class, 'destroy'])->name('destroy');
            });
        });

        // Flush System
        Route::prefix('flush')->group(function (): void {
            Route::name('staff.flush.')->group(function (): void {
                Route::post('/peers', [App\Http\Controllers\Staff\FlushController::class, 'peers'])->name('peers');
                Route::post('/chat', [App\Http\Controllers\Staff\FlushController::class, 'chat'])->name('chat');
            });
        });

        // Forums System
        Route::prefix('forums')->middleware('admin')->group(function (): void {
            Route::name('staff.forums.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ForumController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\ForumController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\ForumController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\ForumController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\ForumController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\ForumController::class, 'destroy'])->name('destroy');
            });
        });

        // Groups System
        Route::prefix('groups')->middleware('admin')->group(function (): void {
            Route::name('staff.groups.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\GroupController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\GroupController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\GroupController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\GroupController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\GroupController::class, 'update'])->name('update');
            });
        });

        // Invites Log
        Route::prefix('invites')->group(function (): void {
            Route::name('staff.invites.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\InviteController::class, 'index'])->name('index');
            });
        });

        // Laravel Log Viewer
        Route::get('/laravel-log', App\Http\Livewire\LaravelLogViewer::class)->middleware('owner')->name('staff.laravellog.index');

        // Mass Actions
        Route::prefix('mass-actions')->group(function (): void {
            Route::get('/validate-users', [App\Http\Controllers\Staff\MassActionController::class, 'update'])->name('staff.mass-actions.validate');
            Route::get('/mass-pm', [App\Http\Controllers\Staff\MassActionController::class, 'create'])->name('staff.mass-pm.create');
            Route::post('/mass-pm/store', [App\Http\Controllers\Staff\MassActionController::class, 'store'])->name('staff.mass-pm.store');
        });

        // Media Lanuages (Languages Used To Populate Language Dropdowns For Subtitles / Audios / Etc.)
        Route::prefix('media-languages')->group(function (): void {
            Route::name('staff.media_languages.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\MediaLanguageController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\MediaLanguageController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\MediaLanguageController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\MediaLanguageController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\MediaLanguageController::class, 'update'])->name('update');
                Route::delete('/{id}/delete', [App\Http\Controllers\Staff\MediaLanguageController::class, 'destroy'])->name('destroy');
            });
        });

        // Moderation System
        Route::prefix('moderation')->group(function (): void {
            Route::name('staff.moderation.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ModerationController::class, 'index'])->name('index');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\ModerationController::class, 'update'])->name('update');
            });
        });

        //Pages System
        Route::prefix('pages')->group(function (): void {
            Route::name('staff.pages.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\PageController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\PageController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\PageController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\PageController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\PageController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\PageController::class, 'destroy'])->name('destroy');
            });
        });

        // Peers
        Route::prefix('peers')->group(function (): void {
            Route::name('staff.peers.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\PeerController::class, 'index'])->name('index');
            });
        });

        // Polls System
        Route::prefix('polls')->group(function (): void {
            Route::name('staff.polls.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\PollController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\Staff\PollController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::get('/create', [App\Http\Controllers\Staff\PollController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\PollController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\PollController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\PollController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\PollController::class, 'destroy'])->name('destroy');
            });
        });

        // Regions
        Route::prefix('regions')->group(function (): void {
            Route::name('staff.regions.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\RegionController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\RegionController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\RegionController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\RegionController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\RegionController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\RegionController::class, 'destroy'])->name('destroy');
            });
        });

        // Registered Seedboxes
        Route::prefix('seedboxes')->group(function (): void {
            Route::name('staff.seedboxes.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\SeedboxController::class, 'index'])->name('index');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\SeedboxController::class, 'destroy'])->name('destroy');
            });
        });

        // Reports
        Route::prefix('reports')->group(function (): void {
            Route::name('staff.reports.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ReportController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\Staff\ReportController::class, 'show'])->where('id', '[0-9]+')->name('show');
                Route::post('/{id}/solve', [App\Http\Controllers\Staff\ReportController::class, 'update'])->name('update');
            });
        });

        // Resolutions
        Route::prefix('resolutions')->group(function (): void {
            Route::name('staff.resolutions.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\ResolutionController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\ResolutionController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\ResolutionController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\ResolutionController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\ResolutionController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\ResolutionController::class, 'destroy'])->name('destroy');
            });
        });

        // RSS System
        Route::prefix('rss')->group(function (): void {
            Route::name('staff.rss.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\RssController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\RssController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\RssController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\RssController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\RssController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\RssController::class, 'destroy'])->name('destroy');
            });
        });

        // Types
        Route::prefix('types')->group(function (): void {
            Route::name('staff.types.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\TypeController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Staff\TypeController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\TypeController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\TypeController::class, 'edit'])->name('edit');
                Route::patch('/{id}/update', [App\Http\Controllers\Staff\TypeController::class, 'update'])->name('update');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\TypeController::class, 'destroy'])->name('destroy');
            });
        });

        // User Staff Notes
        Route::prefix('notes')->group(function (): void {
            Route::name('staff.notes.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\NoteController::class, 'index'])->name('index');
                Route::post('/{username}/store', [App\Http\Controllers\Staff\NoteController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\NoteController::class, 'destroy'])->name('destroy');
            });
        });

        // User Tools TODO: Leaving since we will be refactoring users and roles
        Route::prefix('users')->group(function (): void {
            Route::get('/', [App\Http\Controllers\Staff\UserController::class, 'index'])->name('user_search');
            Route::post('/{username}/edit', [App\Http\Controllers\Staff\UserController::class, 'edit'])->name('user_edit');
            Route::get('/{username}/settings', [App\Http\Controllers\Staff\UserController::class, 'settings'])->name('user_setting');
            Route::post('/{username}/permissions', [App\Http\Controllers\Staff\UserController::class, 'permissions'])->name('user_permissions');
            Route::post('/{username}/password', [App\Http\Controllers\Staff\UserController::class, 'password'])->name('user_password');
            Route::delete('/{username}/destroy', [App\Http\Controllers\Staff\UserController::class, 'destroy'])->name('user_delete');
            Route::post('/{username}/warn', [App\Http\Controllers\Staff\UserController::class, 'warnUser'])->name('user_warn');
        });

        // Warnings Log
        Route::prefix('warnings')->group(function (): void {
            Route::name('staff.warnings.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\WarningController::class, 'index'])->name('index');
            });
        });

        // Internals System
        Route::prefix('internals')->group(function (): void {
            Route::name('staff.internals.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\InternalController::class, 'index'])->name('index');
                Route::get('/{id}/edit', [App\Http\Controllers\Staff\InternalController::class, 'edit'])->name('edit');
                Route::post('/{id}/update', [App\Http\Controllers\Staff\InternalController::class, 'update'])->name('update');
                Route::get('/create', [App\Http\Controllers\Staff\InternalController::class, 'create'])->name('create');
                Route::post('/store', [App\Http\Controllers\Staff\InternalController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\InternalController::class, 'destroy'])->name('destroy');
            });
        });

        // Watchlist
        Route::prefix('watchlist')->group(function (): void {
            Route::name('staff.watchlist.')->group(function (): void {
                Route::get('/', [App\Http\Controllers\Staff\WatchlistController::class, 'index'])->name('index');
                Route::post('/{id}/store', [App\Http\Controllers\Staff\WatchlistController::class, 'store'])->name('store');
                Route::delete('/{id}/destroy', [App\Http\Controllers\Staff\WatchlistController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
