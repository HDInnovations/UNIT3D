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

namespace App\Repositories;

use App\Events\Chatter;
use App\Events\MessageSent;
use App\Http\Resources\ChatMessageResource;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\User;

class ChatRepository
{
    public function message(int $userId, int $roomId, string $message, ?int $bot = null): Message
    {
        $message = Message::query()->create([
            'user_id'     => $userId,
            'chatroom_id' => $roomId,
            'message'     => $message,
            'receiver_id' => null,
            'bot_id'      => $bot,
        ]);

        broadcast(new MessageSent($message));

        return $message;
    }

    public function botMessage(int $botId, string $message, int $receiver): void
    {
        $message = Message::query()->create([
            'bot_id'      => $botId,
            'user_id'     => User::SYSTEM_USER_ID,
            'chatroom_id' => null,
            'message'     => $message,
            'receiver_id' => $receiver,
        ])
            ->refresh()
            ->load([
                'bot',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ]);

        event(new Chatter('new.bot', $receiver, new ChatMessageResource($message)));
        event(new Chatter('new.ping', $receiver, ['type' => 'bot', 'id' => $botId]));
        $message->delete();
    }

    public function privateMessage(int $userId, string $message, int $receiver, ?int $bot = null, ?bool $ignore = null): Message
    {
        $message = Message::query()->create([
            'user_id'     => $userId,
            'chatroom_id' => null,
            'message'     => $message,
            'receiver_id' => $receiver,
            'bot_id'      => $bot,
        ])
            ->refresh()
            ->load([
                'bot',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ]);

        if ($ignore != null) {
            event(new Chatter('new.message', $userId, new ChatMessageResource($message)));
        }

        event(new Chatter('new.message', $receiver, new ChatMessageResource($message)));

        if ($receiver != 1) {
            event(new Chatter('new.ping', $receiver, ['type' => 'target', 'id' => $userId]));
        }

        return $message;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function messages(int $roomId): \Illuminate\Database\Eloquent\Collection
    {
        return Message::query()
            ->with([
                'bot',
                'chatroom',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ])
            ->where('chatroom_id', '=', $roomId)
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function botMessages(int $senderId, int $botId): \Illuminate\Database\Eloquent\Collection
    {
        return Message::query()
            ->with([
                'bot',
                'chatroom',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ])
            ->where(
                fn ($query) => $query
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', '=', $senderId)
                            ->where('receiver_id', '=', User::SYSTEM_USER_ID)
                    )
                    ->orWhere(
                        fn ($query) => $query
                            ->where('user_id', '=', User::SYSTEM_USER_ID)
                            ->where('receiver_id', '=', $senderId)
                    )
            )
            ->where('bot_id', '=', $botId)
            ->whereNull('chatroom_id')
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function privateMessages(int $senderId, int $targetId): \Illuminate\Database\Eloquent\Collection
    {
        return Message::query()
            ->with([
                'bot',
                'chatroom',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ])
            ->where(
                fn ($query) => $query
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', '=', $senderId)
                            ->where('receiver_id', '=', $targetId)
                    )
                    ->orWhere(
                        fn ($query) => $query
                            ->where('user_id', '=', $targetId)
                            ->where('receiver_id', '=', $senderId)
                    )
            )
            ->whereNull('chatroom_id')
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    public function systemMessage(string $message): void
    {
        $systemBotId = Bot::query()->where('command', 'systembot')->value('id');

        $config = config('chat.system_chatroom');

        $systemChatroomId = Chatroom::query()
            ->when(
                \is_int($config),
                fn ($query) => $query->where('id', '=', $config),
                fn ($query) => $query->where('name', '=', $config),
            )
            ->value('id');

        $this->message(User::SYSTEM_USER_ID, $systemChatroomId, $message, $systemBotId);
    }
}
