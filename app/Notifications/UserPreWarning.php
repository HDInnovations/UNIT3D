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

use App\Http\Middleware\RateLimitOutboundMail;
use App\Models\User;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\MaxExceptions;

#[MaxExceptions(1)]
class UserPreWarning extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $_notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(object $_notifiable, string $channel): array
    {
        return match ($channel) {
            'mail'  => [new RateLimitOutboundMail()],
            default => [],
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $_notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting('Hit and run pre-warning')
            ->line('You received a hit and run pre-warning on one or more torrents.')
            ->action('View unsatisfied torrents and start seeding.', route('users.history.index', ['user' => $this->user]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $_notifiable): array
    {
        return [
            'title' => 'Hit and run pre-warning',
            'body'  => 'You received a hit and run pre-warning on one or more torrents. View unsatisfied torrents and start seeding.',
            'url'   => route('users.history.index', ['user' => $this->user]),
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addHours(2);
    }
}
