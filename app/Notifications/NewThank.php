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

use App\Models\Thank;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewThank extends Notification
{
    use Queueable;

    /**
     * NewThank Constructor.
     */
    public function __construct(public string $type, public Thank $thank)
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
        return [
            'title' => $this->thank->user->username.' thanked you for an uploaded torrent',
            'body'  => $this->thank->user->username.' thanked you on torrent: '.$this->thank->torrent->name,
            'url'   => '/torrents/'.$this->thank->torrent->id,
        ];
    }
}
