<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Notifications;

use App\Models\Torrent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class NewReseedRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Models\Torrent
     */
    public Torrent $torrent;
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $configRepository;

    /**
     * Create a new notification instance.
     *
     * @param  Torrent  $torrent
     */
    public function __construct(Torrent $torrent, Repository $configRepository)
    {
        $this->torrent = $torrent;
        $this->configRepository = $configRepository;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return string[]
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return string[]
     */
    public function toArray($notifiable): array
    {
        $appurl = $this->configRepository->get('app.url');

        return [
            'title' => 'New Reseed Request',
            'body'  => sprintf('Some time ago, you downloaded: %s. Now its dead and someone has requested a reseed on it. If you still have this torrent in storage, please consider reseeding it!', $this->torrent->name),
            'url'   => sprintf('%s/torrents/%s', $appurl, $this->torrent->id),
        ];
    }
}
