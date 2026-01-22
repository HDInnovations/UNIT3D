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

class UserWarning extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 1;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(object $notifiable, string $channel): array
    {
        return match ($channel) {
            'mail'  => [new RateLimitOutboundMail()],
            default => [],
        };
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->greeting('Hit and Run Warning Received!')
            ->line('You have received an automated hit and run WARNING on one or more torrents!')
            ->action('View your unsatisfied torrents and seed off your warnings or wait until they expire!', route('users.history.index', ['user' => $this->user]))
            ->line('Thank you for using 🚀'.config('other.title'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hit and Run Warning Received!',
            'body'  => 'You have received an automated hit and run WARNING on one or more torrents! View your unsatisfied torrents and seed off your warnings or wait until they expire!',
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
