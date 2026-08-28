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

function staffAchievement(array $attributes = [], array $thresholds = [10, 20]): Achievement
{
    $achievement = Achievement::query()->create([
        'name'      => 'Staff achievement',
        'category'  => 'Testing',
        'type'      => AchievementConditionType::UPLOAD_COUNT,
        'position'  => 1,
        'is_hidden' => false,
        'enabled'   => true,
        ...$attributes,
    ]);

    foreach ($thresholds as $index => $threshold) {
        AchievementTier::query()->create([
            'achievement_id' => $achievement->id,
            'tier'           => $index + 1,
            'name'           => 'Tier '.($index + 1),
            'description'    => 'Description '.($index + 1),
            'threshold'      => $threshold,
        ]);
    }

    return $achievement;
}

function staffAchievementPayload(array $thresholds = [10, 20], array $tierIds = []): array
{
    return [
        'achievement' => [
            'name'      => 'Posted achievement',
            'category'  => 'Testing',
            'type'      => AchievementConditionType::UPLOAD_COUNT->value,
            'position'  => 3,
            'is_hidden' => false,
            'enabled'   => true,
        ],
        'tiers' => array_map(fn (int $threshold, int $index): array => [
            ...isset($tierIds[$index]) ? ['id' => $tierIds[$index]] : [],
            'name'        => 'Posted tier '.($index + 1),
            'description' => 'Posted description '.($index + 1),
            'threshold'   => $threshold,
        ], $thresholds, array_keys($thresholds)),
    ];
}

it('renders the achievement index', function (): void {
    $this->get(route('staff.achievements.index'))
        ->assertOk()
        ->assertViewIs('Staff.achievement.index')
        ->assertViewHas('achievements');
});

it('renders the achievement creation form', function (): void {
    $this->get(route('staff.achievements.create'))
        ->assertOk()
        ->assertViewIs('Staff.achievement.create')
        ->assertViewHas('conditionTypes');
});

it('renders the achievement edit form', function (): void {
    $achievement = staffAchievement();

    $this->get(route('staff.achievements.edit', $achievement))
        ->assertOk()
        ->assertViewIs('Staff.achievement.edit')
        ->assertViewHas('achievement', $achievement);
});

it('stores an achievement and its tiers', function (): void {
    $this->post(route('staff.achievements.store'), staffAchievementPayload())
        ->assertRedirectToRoute('staff.achievements.index');

    $achievement = Achievement::query()->where('name', '=', 'Posted achievement')->firstOrFail();

    $this->assertDatabaseHas('achievements', ['id' => $achievement->id, 'position' => 3]);
    $this->assertDatabaseHas('achievement_tiers', ['achievement_id' => $achievement->id, 'tier' => 1, 'threshold' => 10]);
    $this->assertDatabaseHas('achievement_tiers', ['achievement_id' => $achievement->id, 'tier' => 2, 'threshold' => 20]);
});

it('rejects descending tier thresholds when storing', function (): void {
    $this->post(route('staff.achievements.store'), staffAchievementPayload([20, 10]))
        ->assertInvalid(['tiers.1.threshold']);

    $this->assertDatabaseMissing('achievements', ['name' => 'Posted achievement']);
});

it('rejects equal consecutive tier thresholds when storing', function (): void {
    $this->post(route('staff.achievements.store'), staffAchievementPayload([10, 10]))
        ->assertInvalid(['tiers.1.threshold']);
});

it('accepts strictly ascending tier thresholds when storing', function (): void {
    $this->post(route('staff.achievements.store'), staffAchievementPayload([10, 11, 12]))
        ->assertValid()
        ->assertRedirectToRoute('staff.achievements.index');

    expect(Achievement::query()->where('name', '=', 'Posted achievement')->firstOrFail()->tiers)->toHaveCount(3);
});

it('applies threshold validation when updating', function (): void {
    $achievement = staffAchievement();
    $ids = $achievement->tiers()->pluck('id')->all();

    $this->patch(route('staff.achievements.update', $achievement), staffAchievementPayload([20, 10], $ids))
        ->assertInvalid(['tiers.1.threshold']);

    $this->patch(route('staff.achievements.update', $achievement), staffAchievementPayload([10, 10], $ids))
        ->assertInvalid(['tiers.1.threshold']);
});

it('can add and remove tiers when updating', function (): void {
    $achievement = staffAchievement([], [10, 20]);
    $keptTier = $achievement->tiers()->where('tier', '=', 1)->firstOrFail();

    $this->patch(route('staff.achievements.update', $achievement), staffAchievementPayload([15, 30], [$keptTier->id]))
        ->assertRedirectToRoute('staff.achievements.index');

    $this->assertDatabaseHas('achievement_tiers', ['id' => $keptTier->id, 'tier' => 1, 'threshold' => 15]);
    $this->assertDatabaseMissing('achievement_tiers', ['achievement_id' => $achievement->id, 'tier' => 2, 'threshold' => 20]);
    $this->assertDatabaseHas('achievement_tiers', ['achievement_id' => $achievement->id, 'tier' => 2, 'threshold' => 30]);
    expect($achievement->tiers()->count())->toBe(2);
});

it('reorders existing tiers without violating the unique tier constraint', function (): void {
    $achievement = staffAchievement([], [10, 20]);
    $ids = $achievement->tiers()->pluck('id')->all();

    $this->patch(
        route('staff.achievements.update', $achievement),
        staffAchievementPayload([10, 20], array_reverse($ids)),
    )->assertRedirectToRoute('staff.achievements.index');

    $tiers = $achievement->tiers()->orderBy('tier')->get();

    expect($tiers->pluck('id')->all())->toBe(array_reverse($ids))
        ->and($tiers->pluck('tier')->all())->toBe([1, 2])
        ->and($tiers->pluck('name')->all())->toBe(['Posted tier 1', 'Posted tier 2'])
        ->and($tiers->pluck('threshold')->map(fn ($threshold): int => (int) $threshold)->all())->toBe([10, 20]);
});

it('leaves user current tier unchanged when tiers are reordered', function (): void {
    $achievement = staffAchievement([], [10, 20]);
    $ids = $achievement->tiers()->pluck('id')->all();
    $userAchievement = UserAchievement::query()->create([
        'user_id'        => User::factory()->create()->id,
        'achievement_id' => $achievement->id,
        'current_tier'   => 2,
        'achieved_at'    => now(),
    ]);

    $this->patch(
        route('staff.achievements.update', $achievement),
        staffAchievementPayload([10, 20], array_reverse($ids)),
    )->assertRedirectToRoute('staff.achievements.index');

    expect($userAchievement->fresh()->current_tier)->toBe(2)
        ->and($achievement->tiers()->where('tier', '=', 2)->value('id'))->toBe($ids[0]);
});

it('destroys an achievement and its tiers', function (): void {
    $achievement = staffAchievement();
    $tierIds = $achievement->tiers()->pluck('id')->all();

    $this->delete(route('staff.achievements.destroy', $achievement))
        ->assertRedirectToRoute('staff.achievements.index');

    $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);

    foreach ($tierIds as $tierId) {
        $this->assertDatabaseMissing('achievement_tiers', ['id' => $tierId]);
    }
});
