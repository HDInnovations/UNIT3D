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

use App\Models\User;
use App\Models\TorrentRequestClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewRequestClaim extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewRequestClaim Constructor.
     */
    public function __construct(public TorrentRequestClaim $claim)
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
        if ($this->claim->user_id === $notifiable->id) {
            return false;
        }

        if ($notifiable->notification?->block_notifications === 1) {
            return false;
        }

        if ($notifiable->notification?->show_request_claim === 0) {
            return false;
        }

        // If the sender's group ID is found in the "Block all notifications from the selected groups" array,
        // the expression will return false.
        return ! \in_array($this->claim->user->group_id, $notifiable->notification?->json_request_groups ?? [], true);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->claim->load('user');

        return [
            'title' => ($this->claim->anon ? 'Anonymous' : $this->claim->user->username).' claimed one of your requested torrents',
            'body'  => ($this->claim->anon ? 'Anonymous' : $this->claim->user->username).' claimed your requested torrent: '.$this->claim->request->name,
            'url'   => \sprintf('/requests/%s', $this->claim->request_id),
        ];
    }
}
