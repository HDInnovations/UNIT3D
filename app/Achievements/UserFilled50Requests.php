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

class UserFilled50Requests extends Achievement
{
    public const string DESCRIPTION_KEY = 'application-messages.achievement.filled50-requests';

    /**
     * The achievement name.
     *
     * @var string
     */
    public $name = 'Filled50Requests';

    /**
     * The amount of "points" this user need to obtain in order to complete this achievement.
     *
     * @var int
     */
    public $points = 50;
}
