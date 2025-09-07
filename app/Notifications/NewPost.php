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

class NewPost extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewPost Constructor.
     */
    public function __construct(public string $type, public User $user, public Post $post)
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

        if ($notifiable->notification?->block_notifications == 1) {
            return false;
        }

        $targetNotification = match ($this->type) {
            'subscription' => 'show_subscription_topic',
            'staff'        => null,
            'topic'        => 'show_forum_topic',
            default        => 'show_forum_topic',
        };

        if ($notifiable->notification?->$targetNotification === 0) {
            return false;
        }

        $targetGroup = match ($this->type) {
            'subscription' => 'json_subscription_groups',
            'staff'        => null,
            'topic'        => 'json_forum_groups',
            default        => 'json_forum_groups',
        };

        // If target group is null (for 'staff'), always return true
        if ($targetGroup === null) {
            return true;
        }

        return ! \in_array($this->post->user->group_id, $notifiable->notification?->$targetGroup ?? [], true);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        $username = ($this->post->anon && !$notifiable->group->is_modo && !$notifiable->is($this->user))
            ? 'Anonymous'
            : $this->user->username;

        if ($this->type == 'subscription') {
            return [
                'title' => $username.' Has Posted In A Subscribed Topic',
                'body'  => $username.' has left a new post in Subscribed Topic '.$this->post->topic->name,
                'url'   => \sprintf('/forums/topics/%s/posts/%s', $this->post->topic->id, $this->post->id),
            ];
        }

        if ($this->type == 'staff') {
            return [
                'title' => $username.' Has Posted In A Staff Forum Topic',
                'body'  => $username.' has left a new post in Staff Topic '.$this->post->topic->name,
                'url'   => \sprintf('%s/posts/%s', route('topics.show', ['id' => $this->post->topic->id]), $this->post->id),
            ];
        }

        return [
            'title' => $username.' Has Posted In A Topic You Started',
            'body'  => $username.' has left a new post in Your Topic '.$this->post->topic->name,
            'url'   => \sprintf('/forums/topics/%s/posts/%s', $this->post->topic->id, $this->post->id),
        ];
    }
}
