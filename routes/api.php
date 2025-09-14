<?php

declare(strict_types=1);

use App\Enums\AuthGuard;
use App\Enums\GlobalRateLimit;
use App\Enums\MiddlewareGroup;
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
Route::middleware(['auth:'.AuthGuard::API->value, 'banned'])->group(function (): void {
    // Torrents System
    Route::prefix('torrents')->group(function (): void {
        Route::get('/', [App\Http\Controllers\API\TorrentController::class, 'index'])->name('api.torrents.index');
        Route::get('/filter', [App\Http\Controllers\API\TorrentController::class, 'filter']);
        Route::get('/{id}', [App\Http\Controllers\API\TorrentController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/upload', [App\Http\Controllers\API\TorrentController::class, 'store']);
    });

    // Requests System
    Route::prefix('requests')->group(function (): void {
        Route::get('/filter', [App\Http\Controllers\API\TorrentRequestController::class, 'filter']);
        Route::get('/{id}', [App\Http\Controllers\API\TorrentRequestController::class, 'show'])->where('id', '[0-9]+');
    });

    // User
    Route::get('/user', [App\Http\Controllers\API\UserController::class, 'show']);
});

// Internal front-end web API routes
Route::name('api.')->middleware([MiddlewareGroup::WEB->value, 'auth', 'banned', 'verified'])->group(function (): void {
    Route::prefix('bookmarks')->name('bookmarks.')->group(function (): void {
        Route::post('/{torrentId}', [App\Http\Controllers\API\BookmarkController::class, 'store'])->name('store');
        Route::delete('/{torrentId}', [App\Http\Controllers\API\BookmarkController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('posts')->name('posts.')->group(function (): void {
        Route::post('/{postId}/like', [App\Http\Controllers\API\LikeController::class, 'store'])->name('like.store');
        Route::post('/{postId}/dislike', [App\Http\Controllers\API\DislikeController::class, 'store'])->name('dislike.store');
    });

    Route::get('/quicksearch', [App\Http\Controllers\API\QuickSearchController::class, 'index'])->name('quicksearch')->middleware('throttle:'.GlobalRateLimit::SEARCH->value)->withoutMiddleware('throttle:'.GlobalRateLimit::WEB->value);
});
