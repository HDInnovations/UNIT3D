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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Notifications\Channels;

use App\Events\MessageSent;
use App\Interfaces\ChatboxNotificationInterface;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\User;

class ChatboxNotificationChannel
{
    /**
     * Send the given notification.
     */
    public function send(Chatroom $notifiable, ChatboxNotificationInterface $notification): void
    {
        $data = $notification->toChatboxNotification($notifiable);

        $systemBotId = Bot::query()->where('command', 'systembot')->soleValue('id');

        $message = Message::create([
            'user_id'     => User::SYSTEM_USER_ID,
            'chatroom_id' => $notifiable->id,
            'message'     => $data['message'],
            'receiver_id' => null,
            'bot_id'      => $systemBotId,
        ]);

        broadcast(new MessageSent($message));
    }
}
