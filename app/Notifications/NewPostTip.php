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

use App\Models\PostTip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewPostTip extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewPostTip Constructor.
     */
    public function __construct(public PostTip $tip)
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->tip->load('sender');

        return [
            'title' => $this->tip->sender->username.' tipped you '.$this->tip->bon.' BON for a forum post',
            'body'  => $this->tip->sender->username.' tipped your forum post in '.$this->tip->post->topic->name,
            'url'   => \sprintf('/forums/topics/%s/posts/%s', $this->tip->post->topic_id, $this->tip->post_id),
        ];
    }
}
