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

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewPostTag extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewPostTag Constructor.
     */
    public function __construct(public Post $post)
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
        // Do not notify self
        if ($this->post->user_id === $notifiable->id) {
            return false;
        }

        // Enforce staff notifications to be sent
        if ($this->post->user->group->is_modo) {
            return true;
        }

        if ($notifiable->notification?->block_notifications === 1) {
            return false;
        }

        if ($notifiable->notification?->show_mention_forum_post === 0) {
            return false;
        }

        // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
        // the expression will return false.
        return ! \in_array($this->post->user->group_id, $notifiable->notification?->json_mention_groups ?? [], true);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $username = $this->post->anon ? 'Anonymous' : $this->post->user->username;
        $title = $this->post->anon ? 'You were tagged in a post' : $username.' tagged you in a post';

        return [
            'title' => $title,
            'body'  => $username.' tagged you in a post in topic '.$this->post->topic->name,
            'url'   => \sprintf('/forums/topics/%s/posts/%s', $this->post->topic->id, $this->post->id),
        ];
    }
}
