<?php

declare(strict_types=1);

use App\Enums\ApiScope;
use App\Enums\AuthGuard;
use App\Enums\GlobalRateLimit;
use App\Enums\MiddlewareGroup;
use App\Http\Middleware\CheckApiScope;
use App\Http\Middleware\CheckIfBanned;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
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
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
if (config('unit3d.proxy_scheme')) {
    URL::forceScheme(config('unit3d.proxy_scheme'));
}

if (config('unit3d.root_url_override')) {
    URL::forceRootUrl(config('unit3d.root_url_override'));
}
Route::middleware([Authenticate::using(AuthGuard::API->value), CheckIfBanned::class])->group(function (): void {
    // Torrents System
    Route::prefix('torrents')->group(function (): void {
        Route::get('/', [App\Http\Controllers\API\TorrentController::class, 'index'])->name('api.torrents.index')->middleware(CheckApiScope::with(ApiScope::CAN_SEARCH, ApiScope::CAN_DOWNLOAD));
        Route::get('/filter', [App\Http\Controllers\API\TorrentController::class, 'filter'])->middleware(CheckApiScope::with(ApiScope::CAN_SEARCH, ApiScope::CAN_DOWNLOAD));
        Route::get('/{id}', [App\Http\Controllers\API\TorrentController::class, 'show'])->where('id', '[0-9]+')->middleware(CheckApiScope::with(ApiScope::CAN_DOWNLOAD));
        Route::post('/upload', [App\Http\Controllers\API\TorrentController::class, 'store'])->middleware(CheckApiScope::with(ApiScope::CAN_UPLOAD));
    });

    // Requests System
    Route::prefix('requests')->group(function (): void {
        Route::get('/filter', [App\Http\Controllers\API\TorrentRequestController::class, 'filter'])->middleware(CheckApiScope::with(ApiScope::CAN_SEARCH));
        Route::get('/{id}', [App\Http\Controllers\API\TorrentRequestController::class, 'show'])->where('id', '[0-9]+');
    });

    // User
    Route::get('/user', [App\Http\Controllers\API\UserController::class, 'show'])->middleware(CheckApiScope::with(ApiScope::CAN_VIEW_USER));
});

// Internal front-end web API routes
Route::name('api.')->middleware([MiddlewareGroup::WEB->value, Authenticate::class, CheckIfBanned::class, EnsureEmailIsVerified::class])->group(function (): void {
    Route::prefix('bookmarks')->name('bookmarks.')->group(function (): void {
        Route::post('/{torrentId}', [App\Http\Controllers\API\BookmarkController::class, 'store'])->name('store');
        Route::delete('/{torrentId}', [App\Http\Controllers\API\BookmarkController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('posts')->name('posts.')->group(function (): void {
        Route::post('/{postId}/like', [App\Http\Controllers\API\LikeController::class, 'store'])->name('like.store');
        Route::post('/{postId}/dislike', [App\Http\Controllers\API\DislikeController::class, 'store'])->name('dislike.store');
    });

    Route::get('/quicksearch', [App\Http\Controllers\API\QuickSearchController::class, 'index'])->name('quicksearch')->middleware(ThrottleRequestsWithRedis::using(GlobalRateLimit::SEARCH))->withoutMiddleware(ThrottleRequestsWithRedis::using(GlobalRateLimit::WEB));
});
