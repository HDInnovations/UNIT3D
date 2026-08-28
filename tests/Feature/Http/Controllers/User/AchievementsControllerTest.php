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

use App\Enums\AchievementConditionType;
use App\Models\Achievement;
use App\Models\AchievementTier;
use App\Models\User;
use App\Models\UserAchievement;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.achievements.index', [$user]));

    $response->assertOk();
    $response->assertViewIs('user.achievement.index');
    $response->assertViewHas('route');
    $response->assertViewHas('user', $user);
    $response->assertViewHas('grouped');
    $response->assertViewHas('earnedCount');
    $response->assertViewHas('completedCount');
    $response->assertViewHas('availableCount');
});

test('index exposes consistent totals and only reveals earned hidden achievements', function (): void {
    $user = User::factory()->create();

    $createAchievement = function (string $name, bool $hidden, bool $enabled, int $tierCount = 1): Achievement {
        $achievement = Achievement::query()->create([
            'name'      => $name,
            'category'  => 'Coverage',
            'type'      => AchievementConditionType::UPLOAD_COUNT,
            'position'  => Achievement::query()->count() + 1,
            'is_hidden' => $hidden,
            'enabled'   => $enabled,
        ]);

        foreach (range(1, $tierCount) as $tier) {
            AchievementTier::query()->create([
                'achievement_id' => $achievement->id,
                'tier'           => $tier,
                'name'           => "{$name} tier {$tier}",
                'description'    => "{$name} description {$tier}",
                'threshold'      => $tier * 10,
            ]);
        }

        return $achievement;
    };

    $visibleEarned = $createAchievement('Visible earned', false, true, 2);
    $createAchievement('Visible unearned', false, true);
    $createAchievement('Hidden unearned secret', true, true);
    $hiddenEarned = $createAchievement('Hidden earned secret', true, true, 2);
    $disabledEarned = $createAchievement('Disabled earned', false, false);
    $completed = $createAchievement('Fully completed', false, true, 2);

    foreach ([[$visibleEarned, 1], [$hiddenEarned, 1], [$disabledEarned, 1], [$completed, 2]] as [$achievement, $tier]) {
        UserAchievement::query()->create([
            'user_id'        => $user->id,
            'achievement_id' => $achievement->id,
            'current_tier'   => $tier,
            'achieved_at'    => now(),
        ]);
    }

    $response = $this->actingAs($user)->get(route('users.achievements.index', $user));

    $response->assertOk()
        ->assertViewHas('earnedCount', 3)
        ->assertViewHas('completedCount', 1)
        ->assertViewHas('availableCount', 4)
        ->assertViewHas('grouped', function ($grouped): bool {
            $rows = $grouped->flatten(1);

            return $rows->contains(fn (array $row): bool => $row['achievement']->name === 'Hidden earned secret')
                && $rows->contains(fn (array $row): bool => $row['achievement']->name === 'Hidden unearned secret' && $row['isHidden']);
        })
        ->assertDontSee('Hidden unearned secret')
        ->assertSee('Hidden earned secret');

    expect($response->viewData('earnedCount'))->toBeLessThanOrEqual($response->viewData('availableCount'));
});
