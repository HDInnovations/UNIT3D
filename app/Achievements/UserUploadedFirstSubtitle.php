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

class UserUploadedFirstSubtitle extends Achievement
{
    public const string DESCRIPTION_KEY = 'application-messages.achievement.uploaded-first-subtitle';

    /**
     * The achievement name.
     *
     * @var string
     */
    public $name = 'UserUploadedFirstSubtitle';

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
    //        $user = User::findOrFail($achiever->id);
    //        $user->seedbonus += 1000;
    //        $user->save();
    //    }
}
