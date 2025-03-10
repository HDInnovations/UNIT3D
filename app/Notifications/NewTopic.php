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

namespace App\Notifications;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewTopic extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewTopic Constructor.
     */
    public function __construct(public string $type, public User $user, public Topic $topic)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(User $notifiable): bool
    {
        if ($notifiable->notification?->block_notifications === 1) {
            return false;
        }

        if ($notifiable->notification?->show_subscription_forum === 0) {
            return false;
        }

        // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
        // the expression will return false.
        return ! \in_array($this->user->group_id, $notifiable->notification?->json_subscription_groups ?? [], true);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->type == 'staff') {
            return [
                'title' => $this->user->username.' Has Posted In A Staff Forum',
                'body'  => $this->user->username.' has started a new staff topic in '.$this->topic->forum->name,
                'url'   => route('topics.show', ['id' => $this->topic->id]),
            ];
        }

        return [
            'title' => $this->user->username.' Has Posted In A Subscribed Forum',
            'body'  => $this->user->username.' has started a new topic in '.$this->topic->forum->name,
            'url'   => \sprintf('/forums/topics/%s', $this->topic->id),
        ];
    }
}
