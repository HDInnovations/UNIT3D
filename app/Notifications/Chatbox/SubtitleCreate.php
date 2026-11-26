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
use App\Models\Subtitle;
use App\Notifications\Channels\ChatboxNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SubtitleCreate extends Notification implements ShouldQueue, ChatboxNotificationInterface
{
    use Queueable;

    public function __construct(public Subtitle $subtitle)
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
        $this->subtitle->load('torrent');

        if ($this->subtitle->anon) {
            return [
                'message' => "An anonymous user has uploaded a new {$this->subtitle->language->name} subtitle for [url=".href_torrent($this->subtitle->torrent)."]{$this->subtitle->torrent->name}[/url]",
            ];
        }

        $this->subtitle->load('user');

        return [
            'message' => '[url='.href_profile($this->subtitle->user)."]{$this->subtitle->user->username}[/url] has uploaded a new subtitle for [url=".href_torrent($this->subtitle->torrent)."]{$this->subtitle->torrent->name}[/url]",
        ];
    }
}
