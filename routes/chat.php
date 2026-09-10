<?php

declare(strict_types=1);

use App\Http\Middleware\CheckIfBanned;
use Illuminate\Auth\Middleware\Authenticate;
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

Route::middleware([Authenticate::class, CheckIfBanned::class])->group(function (): void {
    Route::prefix('chat')->group(function (): void {
        Route::get('/config', [App\Http\Controllers\API\ChatController::class, 'config']);

        /* Statuses */
        Route::get('/statuses', [App\Http\Controllers\API\ChatController::class, 'statuses']);

        /* Rooms */
        Route::get('/rooms', [App\Http\Controllers\API\ChatController::class, 'rooms']);

        /* Bots */
        Route::get('/bots', [App\Http\Controllers\API\ChatController::class, 'bots']);

        /* Audibles */
        Route::post('/audibles/toggle/chatroom', [App\Http\Controllers\API\ChatController::class, 'toggleRoomAudible']);
        Route::post('/audibles/toggle/target', [App\Http\Controllers\API\ChatController::class, 'toggleTargetAudible']);
        Route::post('/audibles/toggle/bot', [App\Http\Controllers\API\ChatController::class, 'toggleBotAudible']);

        /* Conversations */
        Route::get('/conversations', [App\Http\Controllers\API\ChatController::class, 'conversations']);
        Route::post('/conversations/delete/chatroom', [App\Http\Controllers\API\ChatController::class, 'deleteRoomConversation']);
        Route::post('/conversations/delete/target', [App\Http\Controllers\API\ChatController::class, 'deleteTargetConversation']);
        Route::post('/conversations/delete/bot', [App\Http\Controllers\API\ChatController::class, 'deleteBotConversation']);

        /* Messages */
        Route::post('/messages', [App\Http\Controllers\API\ChatController::class, 'createMessage']);
        Route::post('/message/{id}/delete', [App\Http\Controllers\API\ChatController::class, 'deleteMessage'])->whereNumber('id');
        Route::get('/messages/{room_id}', [App\Http\Controllers\API\ChatController::class, 'messages']);

        /* Private Stuff */
        Route::get('/private/messages/{target_id}', [App\Http\Controllers\API\ChatController::class, 'privateMessages'])->whereNumber('target_id');

        /* Bot Stuff */
        Route::get('/bot/{bot_id}', [App\Http\Controllers\API\ChatController::class, 'botMessages'])->whereNumber('bot_id');

        /* Users */
        Route::post('/user/target', [App\Http\Controllers\API\ChatController::class, 'updateUserTarget']);
        Route::post('/user/chatroom', [App\Http\Controllers\API\ChatController::class, 'updateUserRoom']);
        Route::post('/user/bot', [App\Http\Controllers\API\ChatController::class, 'updateBotRoom']);
        Route::post('/user/status', [App\Http\Controllers\API\ChatController::class, 'updateUserChatStatus']);
    });
});
