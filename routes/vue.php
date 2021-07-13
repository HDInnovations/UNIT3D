<?php

use App\Http\Controllers\ChatController;
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
| API Routes For Vue Components
|--------------------------------------------------------------------------
*/

Route::group(function () {
    Route::prefix('chat')->group(function () {
        Route::get('/config', [ChatController::class, 'config']);

        /* Statuses */
        Route::get('/statuses', [ChatController::class, 'statuses']);

        /* Rooms */
        Route::get('/rooms', [ChatController::class, 'rooms']);

        /* Bots */
        Route::get('/bots', [ChatController::class, 'bots']);

        /* Audibles */
        Route::get('/audibles', [ChatController::class, 'audibles']);
        Route::post('/audibles/{user_id}/toggle/chatroom', [ChatController::class, 'toggleRoomAudible']);
        Route::post('/audibles/{user_id}/toggle/target', [ChatController::class, 'toggleTargetAudible']);
        Route::post('/audibles/{user_id}/toggle/bot', [ChatController::class, 'toggleBotAudible']);

        /* Echoes */
        Route::get('/echoes', [ChatController::class, 'echoes']);
        Route::post('/echoes/{user_id}/delete/chatroom', [ChatController::class, 'deleteRoomEcho']);
        Route::post('/echoes/{user_id}/delete/target', [ChatController::class, 'deleteTargetEcho']);
        Route::post('/echoes/{user_id}/delete/bot', [ChatController::class, 'deleteBotEcho']);

        /* Messages */
        Route::post('/messages', [ChatController::class, 'createMessage']);
        Route::get('/message/{id}/delete', [ChatController::class, 'deleteMessage']);
        Route::get('/messages/{room_id}', [ChatController::class, 'messages']);

        /* Private Stuff */
        Route::get('/private/messages/{target_id}', [ChatController::class, 'privateMessages']);

        /* Bot Stuff */
        Route::get('/bot/{bot_id}', [ChatController::class, 'botMessages']);

        /* Users */
        Route::post('/user/{id}/target', [ChatController::class, 'updateUserTarget']);
        Route::post('/user/{id}/chatroom', [ChatController::class, 'updateUserRoom']);
        Route::post('/user/{id}/status', [ChatController::class, 'updateUserChatStatus']);
    });
});
