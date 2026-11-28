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

namespace App\Achievements;

use Assada\Achievements\Achievement;

class UserUploadedFirstSubtitle extends Achievement
{
    /**
     * The achievement name.
     *
     * @var string
     */
    public $name = 'UserUploadedFirstSubtitle';

    /**
     * A small description for the achievement.
     *
     * @var string
     */
    public $description = 'Congratulations! You have made your first subtitle upload!';

    //    /**
    //     * A small description for the award.
    //     *
    //     * @var string
    //     */
    //    public $award = "1000 Bonus Points";

    //    /**
    //     * Triggers whenever an Achiever unlocks this achievement.
    //     *
    //     * @param  AchievementProgress $progress
    //     * @return void
    //     */
    //    public function whenUnlocked($progress)
    //    {
    //        $achiever = $progress->achiever;
    //
    //        $user = User::query()->findOrFail($achiever->id);
    //        $user->seedbonus += 1000;
    //        $user->save();
    //    }
}
