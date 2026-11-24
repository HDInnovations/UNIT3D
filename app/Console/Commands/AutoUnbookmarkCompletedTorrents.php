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

use App\Models\Bookmark;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoUnbookmarkCompletedTorrents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:unbookmark_completed_torrents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unbookmark user torrents automatically upon completion';

    /**
     * Execute the console command.
     */
    final public function handle(): void
    {
        $start = now();

        $affected = DB::transaction(fn (): int => Bookmark::query()
            ->whereRelation('userSetting', 'unbookmark_torrents_on_completion', '=', true)
            ->whereRelation('history', 'completed_at', '>', now()->subDay())
            ->delete(), 5);

        $this->comment($affected.' bookmarks unbookmarked on torrent completion in '.(int) now()->diffInSeconds($start, true).' seconds.');
    }
}
