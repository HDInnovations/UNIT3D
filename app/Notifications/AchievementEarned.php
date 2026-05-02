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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Notifications;

use App\Models\AchievementTier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class AchievementEarned extends Notification
{
    use Queueable;

    public function __construct(
        private readonly AchievementTier $tier,
    ) {
    }

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'          => 'Achievement Unlocked!',
            'body'           => \sprintf('You earned the "%s" achievement!', $this->tier->name),
            'achievement_id' => $this->tier->achievement_id,
            'tier'           => $this->tier->tier,
            'tier_name'      => $this->tier->name,
        ];
    }
}
