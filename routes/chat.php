<?php

declare(strict_types=1);

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
| API Routes For Chat Components
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'banned'])->group(function (): void {
    Route::prefix('chat')->group(function (): void {
        Route::get('/config', [App\Http\Controllers\API\ChatController::class, 'config']);

        /* Statuses */
        Route::get('/statuses', [App\Http\Controllers\API\ChatController::class, 'statuses']);

        /* Rooms */
        Route::get('/rooms', [App\Http\Controllers\API\ChatController::class, 'rooms']);

        /* Bots */
        Route::get('/bots', [App\Http\Controllers\API\ChatController::class, 'bots']);

        /* Audibles */
        Route::get('/audibles', [App\Http\Controllers\API\ChatController::class, 'audibles']);
        Route::post('/audibles/toggle/chatroom', [App\Http\Controllers\API\ChatController::class, 'toggleRoomAudible']);
        Route::post('/audibles/toggle/target', [App\Http\Controllers\API\ChatController::class, 'toggleTargetAudible']);
        Route::post('/audibles/toggle/bot', [App\Http\Controllers\API\ChatController::class, 'toggleBotAudible']);

        /* Echoes */
        Route::get('/echoes', [App\Http\Controllers\API\ChatController::class, 'echoes']);
        Route::post('/echoes/delete/chatroom', [App\Http\Controllers\API\ChatController::class, 'deleteRoomEcho']);
        Route::post('/echoes/delete/target', [App\Http\Controllers\API\ChatController::class, 'deleteTargetEcho']);
        Route::post('/echoes/delete/bot', [App\Http\Controllers\API\ChatController::class, 'deleteBotEcho']);

        /* Messages */
        Route::post('/messages', [App\Http\Controllers\API\ChatController::class, 'createMessage']);
        Route::post('/message/{id}/delete', [App\Http\Controllers\API\ChatController::class, 'deleteMessage']);
        Route::get('/messages/{room_id}', [App\Http\Controllers\API\ChatController::class, 'messages']);

        /* Private Stuff */
        Route::get('/private/messages/{target_id}', [App\Http\Controllers\API\ChatController::class, 'privateMessages']);

        /* Bot Stuff */
        Route::get('/bot/{bot_id}', [App\Http\Controllers\API\ChatController::class, 'botMessages']);

        /* Users */
        Route::post('/user/target', [App\Http\Controllers\API\ChatController::class, 'updateUserTarget']);
        Route::post('/user/chatroom', [App\Http\Controllers\API\ChatController::class, 'updateUserRoom']);
        Route::post('/user/status', [App\Http\Controllers\API\ChatController::class, 'updateUserChatStatus']);
    });
});
