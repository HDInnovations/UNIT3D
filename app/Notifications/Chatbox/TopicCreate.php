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
use App\Models\Topic;
use App\Notifications\Channels\ChatboxNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TopicCreate extends Notification implements ShouldQueue, ChatboxNotificationInterface
{
    use Queueable;

    public function __construct(public Topic $topic, public bool $anon)
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
     * Determine if the notification should be sent.
     */
    public function shouldSend(Chatroom $notifiable): bool
    {
        return $this->topic
            ->forumPermissions()
            ->where('read_topic', '=', true)
            ->whereRelation('group', 'slug', '=', 'user')
            ->exists();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toChatboxNotification(Chatroom $notifiable): array
    {
        if ($this->anon) {
            return [
                'message' => 'An anonymous user has created a new topic [url='.route('topics.show', ['id' => $this->topic->id])."]{$this->topic->name}[/url]",
            ];
        }

        $this->topic->load('user');

        return [
            'message' => '[url='.href_profile($this->topic->user)."]{$this->topic->user->username}[/url] has created a new topic [url=".route('topics.show', ['id' => $this->topic->id])."]{$this->topic->name}[/url]",
        ];
    }
}
