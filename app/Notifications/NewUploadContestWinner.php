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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Notifications;

use App\Models\User;
use App\Models\UploadContest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewUploadContestWinner extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * NewUploadContestWinner Constructor.
     */
    public function __construct(public UploadContest $uploadContest)
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
     * Determine if the notification should be sent.
     */
    public function shouldSend(User $notifiable): bool
    {
        return ! ($notifiable->notification?->block_notifications === 1)
        ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'You have won an upload contest!',
            'body'  => 'Your awards have been added to your account',
            'url'   => route('upload_contests.show', ['uploadContest' => $this->uploadContest]),
        ];
    }
}
