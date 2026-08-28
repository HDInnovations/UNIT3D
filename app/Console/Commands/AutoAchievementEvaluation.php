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

namespace App\Console\Commands;

use App\Enums\AchievementConditionType;
use App\Events\AchievementTierReached;
use App\Models\Achievement;
use App\Models\Torrent;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AutoAchievementEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:achievement_evaluation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically award achievement tiers to users who meet the requirements';

    /**
     * Execute the console command.
     */
    final public function handle(): void
    {
        $now = now();
        $awards = 0;

        $achievements = Achievement::query()
            ->with('tiers')
            ->where('enabled', '=', true)
            ->whereHas('tiers')
            ->orderBy('position')
            ->get();

        foreach ($achievements as $achievement) {
            $awards += $this->evaluate($achievement, $now);
        }

        $elapsed = (int) $now->diffInSeconds(now(), true);
        $this->comment('Automated achievement evaluation command complete, '.$awards.' tiers awarded ('.$elapsed.' s)');
    }

    /**
     * Award every tier of a single achievement that its users now qualify for.
     */
    private function evaluate(Achievement $achievement, \Illuminate\Support\Carbon $now): int
    {
        $playlistId = $achievement->filter_playlist_id;

        if ($playlistId === null && \in_array($achievement->type, [
            AchievementConditionType::PLAYLIST_SEEDING_COUNT,
            AchievementConditionType::PLAYLIST_SEEDING_PERCENT,
        ], true)) {
            $this->warn('Skipping achievement '.$achievement->id.' ('.$achievement->name.'): no playlist selected.');

            return 0;
        }

        $users = $this->metricQuery($achievement);

        $playlistTotal = $achievement->type === AchievementConditionType::PLAYLIST_SEEDING_PERCENT
            ? Torrent::query()->whereHas('playlists', function ($query) use ($playlistId): void {
                $query->where('playlists.id', '=', $playlistId);
            })->count()
            : 0;

        $awards = 0;

        $users->chunkById(100, function ($users) use ($achievement, $playlistTotal, $now, &$awards): void {
            foreach ($users as $user) {
                $currentTier = (int) ($user->userAchievements->first()?->current_tier ?? 0);

                $metric = match ($achievement->type) {
                    AchievementConditionType::UPLOADED_TOTAL           => (float) $user->uploaded,
                    AchievementConditionType::DOWNLOADED_TOTAL         => (float) $user->downloaded,
                    AchievementConditionType::BONUS_POINTS             => (float) $user->seedbonus,
                    AchievementConditionType::ACCOUNT_AGE_DAYS         => (float) (int) $user->created_at->diffInDays($now),
                    AchievementConditionType::PLAYLIST_SEEDING_PERCENT => $playlistTotal > 0
                        ? (float) $user->metric * 100 / $playlistTotal
                        : 0.0,
                    default => (float) ($user->metric ?? 0),
                };

                $newTier = $currentTier;

                // Tiers are ordered by tier number and their thresholds increase (enforced by
                // the form requests), so the first unmet threshold ends the climb.
                foreach ($achievement->tiers as $tier) {
                    if ($tier->tier <= $currentTier) {
                        continue;
                    }

                    if ($metric < (float) $tier->threshold) {
                        break;
                    }

                    $newTier = $tier->tier;
                }

                if ($newTier === $currentTier) {
                    continue;
                }

                UserAchievement::query()->updateOrCreate(
                    ['user_id' => $user->id, 'achievement_id' => $achievement->id],
                    ['current_tier' => $newTier, 'achieved_at' => $now],
                );

                for ($tierLevel = $currentTier + 1; $tierLevel <= $newTier; $tierLevel++) {
                    $awards++;

                    event(new AchievementTierReached($user->id, $achievement->id, $tierLevel));
                }
            }
        });

        return $awards;
    }

    /**
     * Users still in the running for this achievement, with its metric loaded as `metric`.
     *
     * Each metric is one relationship aggregate. The four metrics that are plain columns
     * on `users` need no aggregate and are read straight off the model in evaluate().
     *
     * @return Builder<User>
     */
    private function metricQuery(Achievement $achievement): Builder
    {
        $playlistId = $achievement->filter_playlist_id;

        $users = User::query()
            ->select(['id', 'uploaded', 'downloaded', 'seedbonus', 'created_at'])
            ->with([
                'userAchievements' => fn ($query) => $query->where('achievement_id', '=', $achievement->id),
            ])
            ->whereDoesntHave('userAchievements', function ($query) use ($achievement): void {
                $query
                    ->where('achievement_id', '=', $achievement->id)
                    ->where('current_tier', '>=', $achievement->tiers->max('tier'));
            });

        return match ($achievement->type) {
            AchievementConditionType::UPLOAD_COUNT => $users->withCount([
                'torrents as metric' => fn (Builder $query) => $this->applyTorrentFilters($query, $achievement),
            ]),
            AchievementConditionType::SEEDING_COUNT => $users->withCount([
                'seedingTorrents as metric' => fn (Builder $query) => $this->applyTorrentFilters($query, $achievement),
            ]),
            // A user may seed one torrent from several clients, so `peers` can hold more than
            // one row per torrent. Count the torrents, not the peer rows.
            AchievementConditionType::LAST_SEEDER_COUNT => $users->withAggregate([
                'lastSeederTorrents as metric' => fn (Builder $query) => $this->applyTorrentFilters($query, $achievement),
            ], DB::raw('distinct torrents.id'), 'count'),
            AchievementConditionType::PLAYLIST_SEEDING_COUNT,
            AchievementConditionType::PLAYLIST_SEEDING_PERCENT => $users->withCount([
                'seedingTorrents as metric' => fn (Builder $query) => $query->whereHas(
                    'playlists',
                    function ($query) use ($playlistId): void {
                        $query->where('playlists.id', '=', $playlistId);
                    }
                ),
            ]),
            AchievementConditionType::SEEDSIZE_SUM => $users->withSum('seedingTorrents as metric', 'size'),
            AchievementConditionType::BONUS_SPENT  => $users->withSum('sentBonTransactions as metric', 'cost'),
            // Seedtime is a lifetime figure, so soft-deleted history still counts.
            AchievementConditionType::SEEDTIME_AVG => $users->withAvg([
                'history as metric' => fn ($query) => $query->withTrashed(),
            ], 'seedtime'),
            AchievementConditionType::COMMENT_COUNT   => $users->withCount('comments as metric'),
            AchievementConditionType::REQUESTS_FILLED => $users->withCount([
                'filledRequests as metric' => fn (Builder $query) => $query->whereNotNull('approved_by'),
            ]),
            AchievementConditionType::UPLOADED_TOTAL,
            AchievementConditionType::DOWNLOADED_TOTAL,
            AchievementConditionType::BONUS_POINTS,
            AchievementConditionType::ACCOUNT_AGE_DAYS => $users,
        };
    }

    /**
     * Restrict a torrent aggregate to the achievement's configured torrent filters.
     *
     * @param  Builder<Torrent> $query
     * @return Builder<Torrent>
     */
    private function applyTorrentFilters(Builder $query, Achievement $achievement): Builder
    {
        return $query
            ->when($achievement->filter_type_id, fn (Builder $query, int $value) => $query->where('torrents.type_id', '=', $value))
            ->when($achievement->filter_category_id, fn (Builder $query, int $value) => $query->where('torrents.category_id', '=', $value))
            ->when($achievement->filter_resolution_id, fn (Builder $query, int $value) => $query->where('torrents.resolution_id', '=', $value));
    }
}
