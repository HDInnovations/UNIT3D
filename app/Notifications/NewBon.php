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

use App\Models\Gift;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewBon extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewBon Constructor.
     */
    public function __construct(public Gift $gift)
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
        // Enforce non-anonymous staff notifications to be sent
        if ($this->gift->sender->group->is_modo) {
            return true;
        }

        if ($notifiable->notification?->block_notifications === 1) {
            return false;
        }

        if ($notifiable->notification?->show_bon_gift === 0) {
            return false;
        }

        // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
        // the expression will return false.
        return ! \in_array($this->gift->sender->group_id, $notifiable->notification?->json_bon_groups ?? [], true);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->gift->load('sender');

        return [
            'title' => $this->gift->sender->username.' gifted you '.$this->gift->bon.' BON',
            'body'  => $this->gift->sender->username.' gifted you '.$this->gift->bon.' BON. Note: '.$this->gift->message,
            'url'   => \sprintf('/users/%s', $this->gift->sender->username),
        ];
    }
}
