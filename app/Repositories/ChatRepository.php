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
use Illuminate\Support\Str;

class ChatRepository
{
    public function message(int $userId, int $roomId, string $message, ?int $receiver = null, ?int $bot = null): Message
    {
        if (User::find($userId)->settings->censor) {
            $message = $this->censorMessage($message);
        }

        $message = Message::create([
            'user_id'     => $userId,
            'chatroom_id' => $roomId,
            'message'     => $message,
            'receiver_id' => $receiver,
            'bot_id'      => $bot,
        ]);

        $this->checkMessageLimits($roomId);

        broadcast(new MessageSent($message));

        return $message;
    }

    public function botMessage(int $botId, int $roomId, string $message, ?int $receiver = null): void
    {
        $user = User::find($receiver);

        if ($user->settings->censor) {
            $message = $this->censorMessage($message);
        }

        $save = Message::create([
            'bot_id'      => $botId,
            'user_id'     => 1,
            'chatroom_id' => 0,
            'message'     => $message,
            'receiver_id' => $receiver,
        ]);

        $message = Message::with([
            'bot',
            'user'     => ['group', 'chatStatus'],
            'receiver' => ['group', 'chatStatus'],
        ])->find($save->id);

        event(new Chatter('new.bot', $receiver, new ChatMessageResource($message)));
        event(new Chatter('new.ping', $receiver, ['type' => 'bot', 'id' => $botId]));
        $message->delete();
    }

    public function privateMessage(int $userId, int $roomId, string $message, ?int $receiver = null, ?int $bot = null, ?bool $ignore = null): Message
    {
        if (User::find($userId)->settings->censor) {
            $message = $this->censorMessage($message);
        }

        $save = Message::create([
            'user_id'     => $userId,
            'chatroom_id' => 0,
            'message'     => $message,
            'receiver_id' => $receiver,
            'bot_id'      => $bot,
        ]);

        $message = Message::query()
            ->with([
                'bot',
                'user'     => ['group', 'chatStatus'],
                'receiver' => ['group', 'chatStatus'],
            ])
            ->find($save->id);

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
            ->where('chatroom_id', '!=', 0)
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
            ->where('chatroom_id', '=', 0)
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
            ->where('chatroom_id', '=', 0)
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    public function checkMessageLimits(int $roomId): void
    {
        $messages = $this->messages($roomId);
        $limit = config('chat.message_limit');
        $count = $messages->count();

        // Lets purge all old messages and keep the database to the limit settings
        if ($count > $limit) {
            for ($x = 1; $x <= $count - $limit; $x++) {
                $message = $messages->last();
                echo $message['id']."\n";

                $message = Message::find($message->id);

                if ($message->receiver_id === null) {
                    $message->delete();
                }
            }
        }
    }

    public function systemMessage(string $message, ?int $bot = null): static
    {
        if ($bot) {
            $this->message(User::SYSTEM_USER_ID, $this->systemChatroom(), $message, null, $bot);
        } else {
            $systemBotId = Bot::where('command', 'systembot')->first()->id;

            $this->message(User::SYSTEM_USER_ID, $this->systemChatroom(), $message, null, $systemBotId);
        }

        return $this;
    }

    public function systemChatroom(int|Chatroom|string|null $room = null): int
    {
        $config = config('chat.system_chatroom');

        if ($room !== null) {
            if ($room instanceof Chatroom) {
                $room = $room->id;
            } elseif (\is_int($room)) {
                $room = Chatroom::findOrFail($room)->id;
            } else {
                $room = Chatroom::query()->where('name', '=', $room)->first()->id;
            }
        } elseif (\is_int($config)) {
            $room = Chatroom::findOrFail($config)->id;
        } else {
            $room = Chatroom::query()->where('name', '=', $config)->first()->id;
        }

        return $room;
    }

    protected function censorMessage(string $message): string
    {
        foreach (config('censor.redact') as $word) {
            if (preg_match(\sprintf('/\b%s(?=[.,]|$|\s)/mi', $word), (string) $message)) {
                $message = str_replace($word, \sprintf("<span class='censor'>%s</span>", $word), (string) $message);
            }
        }

        foreach (config('censor.replace') as $word => $replacementWord) {
            if (Str::contains($message, $word)) {
                $message = str_replace($word, $replacementWord, (string) $message);
            }
        }

        return $message;
    }
}
