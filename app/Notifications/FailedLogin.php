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
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class FailedLogin extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * FailedLogin Constructor.
     */
    public function __construct(public string $ip)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(User $notifiable): bool
    {
        return !\in_array($notifiable->group->slug, ['banned', 'validating', 'pruned']);
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return ['ip' => $this->ip, 'time' => Carbon::now()];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())->error()->subject(trans('email.fail-login-subject'))->greeting(trans('email.fail-login-greeting'))->line(trans('email.fail-login-line1'))->line(trans('email.fail-login-line2', ['ip' => $this->ip, 'host' => gethostbyaddr($this->ip), 'time' => Carbon::now()->__toString()]));
    }
}
