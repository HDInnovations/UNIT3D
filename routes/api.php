<?php

use App\Http\Controllers\TorrentController;
use Illuminate\Support\Facades\Route;

/*
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
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

// Torrents System
Route::group(['middleware' => 'auth:api', 'prefix' => 'torrents'], function () {
    Route::get('/', [TorrentController::class, 'index'])->name('torrents.index');
    Route::get('/filter', [TorrentController::class, 'filter']);
    Route::get('/{id}', [TorrentController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/upload', [TorrentController::class, 'store']);
});
