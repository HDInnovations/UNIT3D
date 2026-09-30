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
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChange extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public string $oldEmail, public string $newEmail)
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('application-messages.notification.user-email-change-greeting'))
            ->greeting(__('application-messages.notification.user-email-change-greeting'))
            ->line(__('application-messages.notification.user-email-change-line', [
                'username' => $this->user->username,
                'old'      => $this->oldEmail,
                'new'      => $this->newEmail,
            ]))
            ->action(__('application-messages.notification.helpdesk'), route('tickets.index'))
            ->line(__('application-messages.notification.thank-you-footer', ['site' => config('other.title')]));
    }
}
