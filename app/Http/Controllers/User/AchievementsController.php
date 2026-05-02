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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;

/**
 * @see \Tests\Feature\Http\Controllers\AchievementsControllerTest
 */
class AchievementsController extends Controller
{
    /**
     * Display User Achievements.
     */
    public function index(User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $userAchievements = UserAchievement::query()
            ->with(['achievement.tiers'])
            ->where('user_id', '=', $user->id)
            ->get()
            ->keyBy('achievement_id');

        $achievements = Achievement::query()
            ->with('tiers')
            ->where('enabled', '=', true)
            ->orderBy('positions')
            ->get();

        $achievementRows = $achievements->map(function (Achievement $achievement) use ($userAchievements): array {
            $userAchievement = $userAchievements->get($achievement->id);
            $currentTier = $userAchievement?->current_tier ?? 0;
            $maxTier = $achievement->tiers->max('tier');
            $currentTierDetails = $achievement->tiers->firstWhere('tier', $currentTier);
            $nextTierDetails = $achievement->tiers->firstWhere('tier', $currentTier + 1);
            $iconTier = $currentTierDetails?->icon_path !== null ? $currentTierDetails : null;

            return [
                'achievement'        => $achievement,
                'category'           => $achievement->category,
                'userAchievement'    => $userAchievement,
                'currentTier'        => $currentTier,
                'maxTier'            => $maxTier,
                'currentTierDetails' => $currentTierDetails,
                'nextTierDetails'    => $nextTierDetails,
                'isHidden'           => $achievement->is_hidden && $currentTier === 0,
                'isCompleted'        => $currentTier > 0 && $currentTier >= $maxTier,
                'iconRoute'          => $iconTier !== null
                    ? route('authenticated_images.achievement_tier_image', ['achievementTier' => $iconTier])
                    : ($achievement->icon_path !== null
                        ? route('authenticated_images.achievement_image', ['achievement' => $achievement])
                        : null),
            ];
        });

        $grouped = $achievementRows->groupBy('category');

        $completedCount = $userAchievements
            ->filter(fn (UserAchievement $userAchievement) => $userAchievement->current_tier >= $userAchievement->achievement->tiers->max('tier'))
            ->count();

        return view('user.achievement.index', [
            'route'          => 'achievement',
            'user'           => $user,
            'grouped'        => $grouped,
            'earnedCount'    => $userAchievements->count(),
            'completedCount' => $completedCount,
            'availableCount' => $achievements->where('is_hidden', '=', false)->count(),
        ]);
    }
}
