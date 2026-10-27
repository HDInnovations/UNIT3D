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

use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\User;
use Database\Seeders\ChatroomSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseHas;

test('newly registered user is greeted in chat room', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(ChatroomSeeder::class);
    $user = User::factory()->create();
    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);
    $chatroom = Chatroom::query()
        ->when(
            \is_int(config('chat.system_chatroom')),
            fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
            fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
        )
        ->sole();

    Event::dispatch(new Registered($user));

    assertDatabaseHas('messages', [
        'user_id'     => User::SYSTEM_USER_ID,
        'chatroom_id' => $chatroom->id,
        'bot_id'      => $bot->id,
        'receiver_id' => null,
    ]);

    $conversation = $user->conversations->first();

    expect($conversation)->not->toBeNull()
        ->and($conversation->subject)->toBe(config('welcomepm.subject'));

    $systemMessage = $conversation->messages->first();

    expect($systemMessage)->not->toBeNull()
        ->and($systemMessage->message)->toBe(config('welcomepm.message'))
        ->and($systemMessage->sender_id)->toBe(User::SYSTEM_USER_ID);
});
