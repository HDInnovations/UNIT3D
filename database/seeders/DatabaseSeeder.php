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

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            UserSeeder::class,
            BonExchangeSeeder::class,
            AchievementDetailSeeder::class,
            PageSeeder::class,
            CategorySeeder::class,
            TypeSeeder::class,
            ArticleSeeder::class,
            ForumSeeder::class,
            ForumPermissionSeeder::class,
            ChatroomSeeder::class,
            ChatStatusSeeder::class,
            BotSeeder::class,
            MediaLanguageSeeder::class,
            ResolutionSeeder::class,
            TicketCategorySeeder::class,
            TicketPrioritySeeder::class,
            DistributorSeeder::class,
            RegionSeeder::class,
            OccupationSeeder::class,
            BonEarningSeeder::class,
            BonEarningConditionSeeder::class,
        ]);
    }
}
