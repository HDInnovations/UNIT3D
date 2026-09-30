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

class UserUploaded500Subtitles extends Achievement
{
    public const string DESCRIPTION_KEY = 'application-messages.achievement.uploaded500-subtitles';

    /**
     * The achievement name.
     *
     * @var string
     */
    public $name = 'UserUploaded500Subtitles';

    /**
     * The amount of "points" this user need to obtain in order to complete this achievement.
     *
     * @var int
     */
    public $points = 500;
}
