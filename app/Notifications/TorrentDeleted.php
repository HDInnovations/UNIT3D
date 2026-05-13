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

namespace App\Notifications;

use App\Interfaces\SystemNotificationInterface;
use App\Models\Torrent;
use App\Models\User;
use App\Notifications\Channels\SystemNotificationChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TorrentDeleted extends Notification implements ShouldQueue, SystemNotificationInterface
{
    use Queueable;

    /**
     * @param array<int, string|null> $lastAnnouncedAtByUserId
     */
    public function __construct(public Torrent $torrent, public string $reason, public array $lastAnnouncedAtByUserId = [])
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return class-string
     */
    public function via(object $notifiable): string
    {
        return SystemNotificationChannel::class;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toSystemNotification(User $notifiable): array
    {
        return [
            'subject' => "Torrent deleted: {$this->torrent->name}",
            'message' => <<<BBCODE
            [b]Torrent removed:[/b] {$this->torrent->name} was removed from the site.

            You were listed as an uploader, seeder, or leecher on this torrent. {$this->lastAnnounceMessage($notifiable)} You can remove it from your client if it is still active.
            
            [b]Reason:[/b] {$this->reason}
            BBCODE
        ];
    }

    private function lastAnnounceMessage(User $notifiable): string
    {
        $lastAnnouncedAt = $this->lastAnnouncedAtByUserId[$notifiable->id] ?? null;

        if ($lastAnnouncedAt === null) {
            return 'No last announce timestamp was found for this torrent.';
        }

        $lastAnnouncedAt = Carbon::parse($lastAnnouncedAt);

        return 'Your last announce for this torrent was '.$lastAnnouncedAt->diffForHumans().' ('.$lastAnnouncedAt->utc()->format('Y-m-d H:i:s \U\T\C').').';
    }
}
