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

use App\Models\Torrent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReseedRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewReseedRequest Constructor.
     */
    public function __construct(public Torrent $torrent)
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
        $appurl = config('app.url');

        return [
            'title' => 'Reseed request',
            'body'  => \sprintf('You downloaded this earlier: %s. It is now dead and someone requested a reseed. If you still have it, please reseed.', $this->torrent->name),
            'url'   => \sprintf('%s/torrents/%s', $appurl, $this->torrent->id),
        ];
    }
}
