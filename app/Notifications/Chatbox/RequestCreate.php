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

namespace App\Notifications\Chatbox;

use App\Interfaces\ChatboxNotificationInterface;
use App\Models\Chatroom;
use App\Models\TorrentRequest;
use App\Notifications\Channels\ChatboxNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequestCreate extends Notification implements ShouldQueue, ChatboxNotificationInterface
{
    use Queueable;

    public function __construct(public TorrentRequest $torrentRequest)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return class-string
     */
    public function via(object $notifiable): string
    {
        return ChatboxNotificationChannel::class;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toChatboxNotification(Chatroom $notifiable): array
    {
        if ($this->torrentRequest->anon) {
            return [
                'message' => "An anonymous user has created a new request: [url=".href_request($this->torrentRequest)."]{$this->torrentRequest->name}[/url]",
            ];
        }

        $this->torrentRequest->load('user');

        return [
            'message' => '[url='.href_profile($this->torrentRequest->user)."]{$this->torrentRequest->user->username}[/url] has created a new request: [url=".href_request($this->torrentRequest)."]{$this->torrentRequest->name}[/url]",
        ];
    }
}
