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

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class AchievementTierReached
{
    use Dispatchable;

    public function __construct(
        public readonly int $userId,
        public readonly int $achievementId,
        public readonly int $tier,
    ) {
    }
}
