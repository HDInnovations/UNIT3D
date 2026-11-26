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

namespace App\Listeners;

use App\Models\Chatroom;
use App\Models\User;
use App\Notifications\Chatbox\AchievementUnlock;
use Assada\Achievements\Event\Unlocked;
use Illuminate\Support\Facades\Session;

class AchievementUnlocked
{
    /**
     * Handle the event.
     */
    public function handle(Unlocked $unlocked): void
    {
        // There's an AchievementProgress instance located on $event->progress
        $user = User::query()->findsole($unlocked->progress->achiever_id);

        if (auth()->id() === $user->id) {
            Session::flash('achievement', $unlocked->progress->details->name);
        }

        if ($user->privacy?->private_profile == 0) {
            Chatroom::systemChatroom()->notify(new AchievementUnlock($user, $unlocked));
        }
    }
}
