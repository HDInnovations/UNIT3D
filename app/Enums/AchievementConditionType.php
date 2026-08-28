<?php

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

declare(strict_types=1);

namespace App\Enums;

enum AchievementConditionType: string
{
    case UPLOAD_COUNT = 'upload_count';
    case UPLOADED_TOTAL = 'uploaded_total';
    case DOWNLOADED_TOTAL = 'downloaded_total';
    case SEEDTIME_AVG = 'seedtime_avg';
    case SEEDSIZE_SUM = 'seedsize_sum';
    case BONUS_POINTS = 'bonus_points';
    case BONUS_SPENT = 'bonus_spent';
    case REQUESTS_FILLED = 'requests_filled';
    case COMMENT_COUNT = 'comment_count';
    case ACCOUNT_AGE_DAYS = 'account_age_days';
    case LAST_SEEDER_COUNT = 'last_seeder_count';
    case SEEDING_COUNT = 'seeding_count';
    case PLAYLIST_SEEDING_COUNT = 'playlist_seeding_count';
    case PLAYLIST_SEEDING_PERCENT = 'playlist_seeding_percent';

    public function label(): string
    {
        return match ($this) {
            self::UPLOAD_COUNT             => 'Upload Count',
            self::UPLOADED_TOTAL           => 'Uploaded Total (bytes)',
            self::DOWNLOADED_TOTAL         => 'Downloaded Total (bytes)',
            self::SEEDTIME_AVG             => 'Seedtime Average (seconds)',
            self::SEEDSIZE_SUM             => 'Seedsize Sum (bytes)',
            self::BONUS_POINTS             => 'Bonus Points',
            self::BONUS_SPENT              => 'Bonus Points Spent',
            self::REQUESTS_FILLED          => 'Requests Filled',
            self::COMMENT_COUNT            => 'Comment Count',
            self::ACCOUNT_AGE_DAYS         => 'Account Age (days)',
            self::LAST_SEEDER_COUNT        => 'Last Seeder Count',
            self::SEEDING_COUNT            => 'Seeding Count (distinct torrents)',
            self::PLAYLIST_SEEDING_COUNT   => 'Playlist Seeding Count',
            self::PLAYLIST_SEEDING_PERCENT => 'Playlist Seeding Percent',
        };
    }
}
