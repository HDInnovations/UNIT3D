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

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use App\Models\AchievementTier;
use App\Models\User;
use App\Models\UserAchievement;

test('profile hidden counts exclude earned disabled achievements', function (): void {
    $user = User::factory()->create();
    $viewer = User::factory()->create();

    $hiddenEnabled = Achievement::query()->create([
        'name'      => 'Enabled secret',
        'category'  => 'Coverage',
        'type'      => AchievementConditionType::UPLOAD_COUNT,
        'position'  => 1,
        'is_hidden' => true,
        'enabled'   => true,
    ]);
    $hiddenDisabled = Achievement::query()->create([
        'name'      => 'Disabled earned secret',
        'category'  => 'Coverage',
        'type'      => AchievementConditionType::UPLOAD_COUNT,
        'position'  => 2,
        'is_hidden' => true,
        'enabled'   => false,
    ]);

    foreach ([$hiddenEnabled, $hiddenDisabled] as $achievement) {
        AchievementTier::query()->create([
            'achievement_id' => $achievement->id,
            'tier'           => 1,
            'name'           => 'Secret tier',
            'description'    => 'Secret description',
            'threshold'      => 1,
        ]);
    }

    UserAchievement::query()->create([
        'user_id'        => $user->id,
        'achievement_id' => $hiddenDisabled->id,
        'current_tier'   => 1,
        'achieved_at'    => now(),
    ]);

    $this->actingAs($viewer)->get(route('users.show', $user))
        ->assertOk()
        ->assertViewIs('user.profile.show')
        ->assertViewHas('achievementsTotal', 1)
        ->assertViewHas('achievementsEarned', 0)
        ->assertViewHas('secretLockedCount', 1);
});
