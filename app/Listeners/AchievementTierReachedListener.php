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

namespace App\Listeners;

use App\Events\AchievementTierReached;
use App\Models\AchievementTier;
use App\Models\User;
use App\Notifications\AchievementEarned;
use App\Repositories\ChatRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class AchievementTierReachedListener implements ShouldQueue
{
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    public function handle(AchievementTierReached $event): void
    {
        $user = User::query()->find($event->userId);

        if ($user === null) {
            return;
        }

        $tier = AchievementTier::query()
            ->where('achievement_id', '=', $event->achievementId)
            ->where('tier', '=', $event->tier)
            ->first();

        if ($tier === null) {
            return;
        }

        $user->notify(new AchievementEarned($tier));

        if ($user->privacy?->private_profile == 0) {
            $profileUrl = href_profile($user);

            $this->chatRepository->systemMessage(
                \sprintf('User [url=%s]%s[/url] has earned the %s achievement!', $profileUrl, $user->username, $tier->name),
            );
        }
    }
}
