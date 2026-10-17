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

use App\Interfaces\SystemNotificationInterface;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\User;

class SystemNotificationChannel
{
    /**
     * Send the given notification.
     */
    public function send(User $notifiable, SystemNotificationInterface $notification): void
    {
        $data = $notification->toSystemNotification($notifiable);

        $conversation = Conversation::query()->create(['subject' => $data['subject']]);

        $conversation->users()->sync([User::SYSTEM_USER_ID => ['read' => true], $notifiable->id]);

        PrivateMessage::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id'       => User::SYSTEM_USER_ID,
            'message'         => $data['message'],
        ]);
    }
}
