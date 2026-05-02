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
use App\Enums\ModerationStatus;
use App\Events\AchievementTierReached;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Closure;

class AutoAchievementEvaluation extends Command
{
    protected $signature = 'auto:achievement_evaluation';

    protected $description = 'Evaluates all enabled achievements and awards tiers to qualifying users.';

    public function handle(): void
    {
        $now = now();
        $totalAwards = 0;

        $achievements = Achievement::query()
            ->with('tiers')
            ->where('enabled', '=', true)
            ->orderBy('positions')
            ->get()
            ->filter(fn (Achievement $achievement): bool => $achievement->tiers->isNotEmpty());

        if ($achievements->isEmpty()) {
            $this->comment('No enabled achievements with tiers.');

            return;
        }

        $playlistTotals = [];

        foreach ($achievements as $achievement) {
            if (
                $achievement->type === AchievementConditionType::PLAYLIST_SEEDING_PERCENT
                && $achievement->filter_playlist_id !== null
            ) {
                $playlistTotals[$achievement->id] = (int) DB::table('playlist_torrents')
                    ->where('playlist_id', '=', $achievement->filter_playlist_id)
                    ->count();
            }
        }

        $existing = UserAchievement::query()
            ->whereIn('achievement_id', $achievements->pluck('id'))
            ->get(['user_id', 'achievement_id', 'current_tier'])
            ->groupBy('achievement_id')
            ->map(fn ($rows) => $rows->pluck('current_tier', 'user_id'));

        $query = User::query()
            ->select(['id', 'uploaded', 'downloaded', 'seedbonus', 'created_at'])
            ->whereNull('deleted_at');

        foreach ($achievements as $achievement) {
            $spec = $achievement->type->aggregateSpec();

            if ($spec === null) {
                continue;
            }

            $alias = 'ach_metric_'.$achievement->id;
            $constraint = $this->metricConstraint($achievement);

            match ($spec['fn']) {
                'count' => $query->withCount([$spec['relation'].' as '.$alias => $constraint]),
                'sum'   => $query->withSum([$spec['relation'].' as '.$alias => $constraint], $spec['column']),
                'avg'   => $query->withAvg([$spec['relation'].' as '.$alias => $constraint], $spec['column']),
            };
        }

        $query->each(function (User $user) use ($achievements, $existing, $playlistTotals, &$totalAwards): void {
            foreach ($achievements as $achievement) {
                $awarded = $existing->get($achievement->id);
                $currentTier = (int) ($awarded?->get($user->id) ?? 0);
                $maxTier = (int) $achievement->tiers->max('tier');

                if ($currentTier >= $maxTier) {
                    continue;
                }

                $alias = 'ach_metric_'.$achievement->id;
                $metric = match ($achievement->type) {
                    AchievementConditionType::UPLOADED_TOTAL           => (float) $user->uploaded,
                    AchievementConditionType::DOWNLOADED_TOTAL         => (float) $user->downloaded,
                    AchievementConditionType::BONUS_POINTS             => (float) $user->seedbonus,
                    AchievementConditionType::ACCOUNT_AGE_DAYS         => (float) (int) $user->created_at->diffInDays(now()),
                    AchievementConditionType::PLAYLIST_SEEDING_PERCENT => (float) ($user->{$alias} ?? 0) * 100.0
                        / max($playlistTotals[$achievement->id] ?? 1, 1),
                    default => (float) ($user->{$alias} ?? 0),
                };

                $newTier = $currentTier;

                foreach ($achievement->tiers->sortBy('tier') as $tier) {
                    if ($tier->tier <= $currentTier) {
                        continue;
                    }

                    if ($metric >= (float) $tier->threshold) {
                        $newTier = $tier->tier;
                    } else {
                        break;
                    }
                }

                if ($newTier > $currentTier) {
                    UserAchievement::query()->updateOrCreate(
                        ['user_id' => $user->id, 'achievement_id' => $achievement->id],
                        ['current_tier' => $newTier, 'achieved_at' => now()],
                    );

                    for ($tierLevel = $currentTier + 1; $tierLevel <= $newTier; $tierLevel++) {
                        $totalAwards++;

                        event(new AchievementTierReached($user->id, $achievement->id, $tierLevel));
                    }
                }
            }
        }, 100);

        $elapsed = (int) now()->diffInMilliseconds($now, true);

        $this->comment(\sprintf(
            'Achievement evaluation complete: %d achievements evaluated, %d new awards (%d ms)',
            $achievements->count(),
            $totalAwards,
            $elapsed,
        ));
    }

    private function metricConstraint(Achievement $achievement): Closure
    {
        $type = $achievement->type;

        return function ($query) use ($achievement, $type): void {
            if ($type === AchievementConditionType::SEEDTIME_AVG) {
                $query->withTrashed();
            }

            if ($type === AchievementConditionType::UPLOAD_COUNT) {
                $query->where('status', '=', ModerationStatus::APPROVED->value);
            }

            if ($type === AchievementConditionType::REQUESTS_FILLED) {
                $query->whereNotNull('approved_by');
            }

            if ($type->honorsTorrentFilters()) {
                $query
                    ->when($achievement->filter_type_id, fn ($query, $value) => $query->where('type_id', '=', $value))
                    ->when($achievement->filter_category_id, fn ($query, $value) => $query->where('category_id', '=', $value))
                    ->when($achievement->filter_resolution_id, fn ($query, $value) => $query->where('resolution_id', '=', $value));
            }

            if (\in_array($type, [AchievementConditionType::PLAYLIST_SEEDING_COUNT, AchievementConditionType::PLAYLIST_SEEDING_PERCENT], true)) {
                $query->whereExists(fn ($sub) => $sub->selectRaw('1')
                    ->from('playlist_torrents')
                    ->whereColumn('playlist_torrents.torrent_id', 'torrents.id')
                    ->where('playlist_torrents.playlist_id', '=', $achievement->filter_playlist_id ?? 0));
            }
        };
    }
}
