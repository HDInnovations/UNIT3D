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

use App\Models\Ban;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBan extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ban $ban)
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
        $chatUrl = config('unit3d.chat-link-url');

        return (new MailMessage())
            ->subject(__('application-messages.notification.user-ban-greeting'))
            ->greeting(__('application-messages.notification.user-ban-greeting'))
            ->line(__('application-messages.notification.user-ban-line', ['site' => config('other.title'), 'reason' => $this->ban->ban_reason]))
            ->action(__('application-messages.notification.need-support'), $chatUrl)
            ->line(__('application-messages.notification.thank-you-footer', ['site' => config('other.title')]));
    }
}
