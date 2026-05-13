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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TorrentsDeleted extends Notification implements ShouldQueue, SystemNotificationInterface
{
    use Queueable;

    /**
     * @param Collection<int, Torrent>            $torrents
     * @param array<int, array<int, string|null>> $lastAnnouncedAtByUserIdAndTorrentId
     */
    public function __construct(public Collection $torrents, public string $title, public string $reason, public array $lastAnnouncedAtByUserIdAndTorrentId = [])
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
            'subject' => 'Bulk Torrents Deleted - '.$this->title.'! ',
            'message' => <<<BBCODE
            [b]Attention:[/b] The following torrents have been removed from our site.

            [list]
            [*]{$this->torrentLines($notifiable)}
            [/list]

            Our system shows that you were either the uploader, a seeder or a leecher on said torrent. You can safely remove it from your client if it is still active.

            [b]Removal Reason:[/b] {$this->reason}
            BBCODE
        ];
    }

    private function torrentLines(User $notifiable): string
    {
        return $this->torrents
            ->map(fn (Torrent $torrent) => $torrent->name.$this->lastAnnounceSuffix($notifiable, $torrent))
            ->join("\n[*]");
    }

    private function lastAnnounceSuffix(User $notifiable, Torrent $torrent): string
    {
        $lastAnnouncedAt = $this->lastAnnouncedAtByUserIdAndTorrentId[$notifiable->id][$torrent->id] ?? null;

        if ($lastAnnouncedAt === null) {
            return ' (last announce: not found)';
        }

        $lastAnnouncedAt = Carbon::parse($lastAnnouncedAt);

        return ' (last announce: '.$lastAnnouncedAt->diffForHumans().' on '.$lastAnnouncedAt->utc()->format('Y-m-d H:i:s \U\T\C').')';
    }
}
