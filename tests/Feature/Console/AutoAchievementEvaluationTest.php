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
use App\Enums\ModerationStatus;
use App\Events\AchievementTierReached;
use App\Models\Achievement;
use App\Models\AchievementTier;
use App\Models\BonTransactions;
use App\Models\Comment;
use App\Models\History;
use App\Models\Playlist;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\Type;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

// These tests cover the evaluation command. The queued listener announces tiers in chat and
// needs the bot/chatroom seeders, so the event is faked here and asserted on directly.
beforeEach(function (): void {
    Event::fake([AchievementTierReached::class]);
});

/**
 * Build an enabled achievement whose tier N has thresholds[N - 1].
 *
 * @param array<int, float|int>   $thresholds
 * @param array<string, int|null> $attributes
 */
function achievementWithTiers(AchievementConditionType $type, array $thresholds, array $attributes = []): Achievement
{
    $achievement = Achievement::query()->create([
        'name'      => 'Test '.$type->value,
        'category'  => 'Testing',
        'type'      => $type,
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
            'description'    => 'Tier '.($index + 1),
            'threshold'      => $threshold,
        ]);
    }

    return $achievement;
}

/** The tier the user currently holds for the achievement, or 0 when unawarded. */
function currentTier(User $user, Achievement $achievement): int
{
    return (int) UserAchievement::query()
        ->where('user_id', '=', $user->id)
        ->where('achievement_id', '=', $achievement->id)
        ->value('current_tier');
}

/** A seeding history row, since the factory randomises seeder/active. */
function seedingHistory(User $user, Torrent $torrent, int $seedtime = 0): History
{
    return History::factory()->create([
        'user_id'    => $user->id,
        'torrent_id' => $torrent->id,
        'seeder'     => 1,
        'active'     => 1,
        'seedtime'   => $seedtime,
    ]);
}

it('awards a tier for an approved upload count and ignores unapproved torrents', function (): void {
    $user = User::factory()->create();

    Torrent::factory()->create(['user_id' => $user->id]);
    Torrent::factory()->create(['user_id' => $user->id, 'status' => ModerationStatus::PENDING]);

    $achievement = achievementWithTiers(AchievementConditionType::UPLOAD_COUNT, [1, 2]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('applies the type filter to an upload count', function (): void {
    $user = User::factory()->create();
    $type = Type::factory()->create();

    Torrent::factory()->create(['user_id' => $user->id, 'type_id' => $type->id]);
    Torrent::factory()->create(['user_id' => $user->id, 'type_id' => Type::factory()->create()->id]);

    $achievement = achievementWithTiers(AchievementConditionType::UPLOAD_COUNT, [1, 2], [
        'filter_type_id' => $type->id,
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('awards every crossed tier in a single run', function (): void {
    $user = User::factory()->create(['uploaded' => 1000]);
    $achievement = achievementWithTiers(AchievementConditionType::UPLOADED_TOTAL, [100, 500, 1000, 5000]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(3);

    Event::assertDispatchedTimes(AchievementTierReached::class, 3);
});

it('stops climbing at the first unmet threshold', function (): void {
    $user = User::factory()->create(['uploaded' => 1000]);

    // Out-of-order thresholds are rejected by the form requests, but if a row is ever
    // written directly the climb must not skip past an unmet tier.
    $achievement = achievementWithTiers(AchievementConditionType::UPLOADED_TOTAL, [100, 5000, 900]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))
        ->toBe(1, 'tier 2 is unmet, so tier 3 must not be awarded');
});

it('counts soft deleted history towards average seedtime', function (): void {
    $user = User::factory()->create();

    seedingHistory($user, Torrent::factory()->create(), 100);
    $deleted = seedingHistory($user, Torrent::factory()->create(), 300);

    // history has a composite primary key, so soft delete through a query.
    History::query()
        ->where('user_id', '=', $deleted->user_id)
        ->where('torrent_id', '=', $deleted->torrent_id)
        ->delete();

    $achievement = achievementWithTiers(AchievementConditionType::SEEDTIME_AVG, [200]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('sums the size of actively seeded torrents', function (): void {
    $user = User::factory()->create();

    seedingHistory($user, Torrent::factory()->create(['size' => 400]));
    seedingHistory($user, Torrent::factory()->create(['size' => 600]));

    $achievement = achievementWithTiers(AchievementConditionType::SEEDSIZE_SUM, [1000, 1001]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('sums bonus points the user has spent', function (): void {
    $user = User::factory()->create();

    BonTransactions::factory()->create(['sender_id' => $user->id, 'cost' => 40]);
    BonTransactions::factory()->create(['sender_id' => $user->id, 'cost' => 60]);
    BonTransactions::factory()->create(['receiver_id' => $user->id, 'cost' => 900]);

    $achievement = achievementWithTiers(AchievementConditionType::BONUS_SPENT, [100, 101]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('counts only approved request fills', function (): void {
    $user = User::factory()->create();

    TorrentRequest::factory()->create(['filled_by' => $user->id]);
    TorrentRequest::factory()->create(['filled_by' => $user->id, 'approved_by' => null]);

    $achievement = achievementWithTiers(AchievementConditionType::REQUESTS_FILLED, [1, 2]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('counts the comments a user has written', function (): void {
    $user = User::factory()->create();

    Comment::factory()->count(3)->create(['user_id' => $user->id]);

    $achievement = achievementWithTiers(AchievementConditionType::COMMENT_COUNT, [3, 4]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('counts distinct torrents seeded, with the resolution filter applied', function (): void {
    $user = User::factory()->create();
    $torrent = Torrent::factory()->create();

    seedingHistory($user, $torrent);
    seedingHistory($user, Torrent::factory()->create());

    $achievement = achievementWithTiers(AchievementConditionType::SEEDING_COUNT, [1, 2], [
        'filter_resolution_id' => $torrent->resolution_id,
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('counts a torrent once when the user seeds it from several clients', function (): void {
    $user = User::factory()->create();
    $torrent = Torrent::factory()->create(['seeders' => 1]);

    // The peers primary key is (user_id, torrent_id, peer_id), so one torrent seeded
    // from two clients is two rows. The metric must still be 1.
    foreach (['client-one', 'client-two'] as $peerId) {
        DB::table('peers')->insert([
            'peer_id'     => $peerId,
            'ip'          => '127.0.0.1',
            'port'        => 6881,
            'agent'       => 'test',
            'uploaded'    => 0,
            'downloaded'  => 0,
            'left'        => 0,
            'seeder'      => 1,
            'active'      => 1,
            'connectable' => 1,
            'torrent_id'  => $torrent->id,
            'user_id'     => $user->id,
        ]);
    }

    $achievement = achievementWithTiers(AchievementConditionType::LAST_SEEDER_COUNT, [1, 2]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))
        ->toBe(1, 'two peer rows for one torrent must count as one torrent, not two');
});

it('counts torrents seeded from a given playlist', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create();
    $inPlaylist = Torrent::factory()->create();

    $playlist->torrents()->attach($inPlaylist->id, ['tmdb_id' => 0]);

    seedingHistory($user, $inPlaylist);
    seedingHistory($user, Torrent::factory()->create());

    $achievement = achievementWithTiers(AchievementConditionType::PLAYLIST_SEEDING_COUNT, [1, 2], [
        'filter_playlist_id' => $playlist->id,
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('awards a playlist completion percentage', function (): void {
    $user = User::factory()->create();
    $playlist = Playlist::factory()->create();

    $torrents = Torrent::factory()->count(4)->create();

    foreach ($torrents as $index => $torrent) {
        $playlist->torrents()->attach($torrent->id, ['tmdb_id' => $index]);
    }

    foreach ($torrents->take(3) as $torrent) {
        seedingHistory($user, $torrent);
    }

    // 3 of 4 == 75%
    $achievement = achievementWithTiers(AchievementConditionType::PLAYLIST_SEEDING_PERCENT, [50, 75, 100], [
        'filter_playlist_id' => $playlist->id,
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(2);
});

it('skips a playlist achievement that has no playlist selected', function (): void {
    $user = User::factory()->create();
    $achievement = achievementWithTiers(AchievementConditionType::PLAYLIST_SEEDING_PERCENT, [0]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(0);
});

it('awards an account age tier', function (): void {
    $user = User::factory()->create(['created_at' => now()->subDays(30)]);
    $achievement = achievementWithTiers(AchievementConditionType::ACCOUNT_AGE_DAYS, [30, 365]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(1);
});

it('does not re-award or re-announce on a second run', function (): void {
    $user = User::factory()->create(['uploaded' => 1000]);
    $achievement = achievementWithTiers(AchievementConditionType::UPLOADED_TOTAL, [100, 500]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    $awardedAt = UserAchievement::query()
        ->where('user_id', '=', $user->id)
        ->where('achievement_id', '=', $achievement->id)
        ->value('achieved_at');

    Event::fake([AchievementTierReached::class]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(2)
        ->and(UserAchievement::query()->where('user_id', '=', $user->id)->count())->toBe(1)
        ->and(UserAchievement::query()->where('user_id', '=', $user->id)->value('achieved_at'))->toEqual($awardedAt);

    Event::assertNotDispatched(AchievementTierReached::class);
});

it('leaves a user who already holds the top tier untouched', function (): void {
    $user = User::factory()->create(['uploaded' => 100_000]);
    $achievement = achievementWithTiers(AchievementConditionType::UPLOADED_TOTAL, [100, 500]);

    UserAchievement::query()->create([
        'user_id'        => $user->id,
        'achievement_id' => $achievement->id,
        'current_tier'   => 2,
        'achieved_at'    => now()->subDay(),
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(2);

    Event::assertNotDispatched(AchievementTierReached::class);
});

it('ignores achievements that are disabled or have no tiers', function (): void {
    $user = User::factory()->create(['uploaded' => 100_000]);

    $disabled = achievementWithTiers(AchievementConditionType::UPLOADED_TOTAL, [1]);
    $disabled->update(['enabled' => false]);

    $tierless = Achievement::query()->create([
        'name'      => 'Tierless',
        'category'  => 'Testing',
        'type'      => AchievementConditionType::UPLOADED_TOTAL,
        'position'  => 2,
        'is_hidden' => false,
        'enabled'   => true,
    ]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $disabled))->toBe(0)
        ->and(currentTier($user, $tierless))->toBe(0);
});

it('awards tiers based on total downloaded bytes', function (): void {
    $user = User::factory()->create(['downloaded' => 1000]);
    $achievement = achievementWithTiers(AchievementConditionType::DOWNLOADED_TOTAL, [100, 1000, 1001]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(2);
});

it('awards tiers based on the current bonus point balance', function (): void {
    $user = User::factory()->create(['seedbonus' => 250.5]);
    $achievement = achievementWithTiers(AchievementConditionType::BONUS_POINTS, [100, 250.5, 251]);

    $this->artisan('auto:achievement_evaluation')->assertSuccessful();

    expect(currentTier($user, $achievement))->toBe(2);
});
