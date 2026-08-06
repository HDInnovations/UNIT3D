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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('auto:cache_user_leech_counts')]
#[Description('Caches user leech counts')]
class AutoCacheUserLeechCounts extends Command
{
    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $peerCounts = User::query()
            ->withoutGlobalScopes()
            ->selectRaw("'user-leeching-count:' || id as cacheKey")
            ->selectRaw('(select COUNT(*) from peers where peers.user_id = users.id and seeder = FALSE and active = TRUE and visible = TRUE) as count')
            ->pluck('count', 'cacheKey')
            ->toArray();

        cache()->putMany($peerCounts);

        $this->comment(\count($peerCounts).' user leech counts cached.');
    }
}
