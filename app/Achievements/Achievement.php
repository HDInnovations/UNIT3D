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
 * @author     BackendI18n
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Achievements;

use Assada\Achievements\Achievement as VendorAchievement;
use Assada\Achievements\Model\AchievementDetails;

/**
 * First-party base class for every achievement.
 *
 * The vendor package persists `$description` verbatim into the shared
 * `achievement_details` table the first time each achievement class is
 * instantiated (see Achievement::getModel()), and every view renders that
 * stored column afterwards. Baking a request-locale translation into
 * `$description` would therefore freeze whichever locale happened to
 * instantiate the class first (or silently re-freeze it on every request
 * under `achievements.auto_sync`), and would never retroactively fix rows
 * seeded before this migration.
 *
 * Subclasses instead declare a `DESCRIPTION_KEY` translation key. The
 * constructor seeds the shared column with a fixed, canonical English
 * string (so the stored metadata never depends on the instantiator's
 * locale), while presentation code MUST call {@see self::descriptionFor()}
 * on the persisted {@see AchievementDetails} row to resolve the
 * description in the viewer's current locale, without ever instantiating
 * the achievement class.
 */
abstract class Achievement extends VendorAchievement
{
    /**
     * Translation key (in lang/{locale}/application-messages.php) for this
     * achievement's description. Subclasses MUST override this.
     */
    public const string DESCRIPTION_KEY = '';

    public function __construct()
    {
        // Canonical, locale-independent value stored in achievement_details.
        // Never displayed directly; see descriptionFor().
        $this->description = trans(static::DESCRIPTION_KEY, [], 'en');

        parent::__construct();
    }

    /**
     * Resolve this achievement's description in the given locale (or the
     * current app locale when omitted), without instantiating the
     * achievement (avoiding the sync side effect in the vendor
     * constructor).
     */
    public static function localizedDescription(?string $locale = null): string
    {
        return trans(static::DESCRIPTION_KEY, [], $locale);
    }

    /**
     * Resolve the display description for a persisted achievement_details
     * row in the given locale (or the current app locale when omitted).
     *
     * Falls back to the row's stored `description` column when the class
     * behind it no longer exists, isn't one of ours, or has been removed
     * (e.g. third-party/custom achievements, or legacy rows), so nothing
     * regresses for content this migration doesn't own.
     */
    public static function descriptionFor(AchievementDetails $details, ?string $locale = null): string
    {
        $className = $details->class_name;

        if (\is_string($className) && \is_subclass_of($className, self::class)) {
            return $className::localizedDescription($locale);
        }

        return (string) $details->description;
    }
}
