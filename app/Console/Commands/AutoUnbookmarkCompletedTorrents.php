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
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('auto:unbookmark_completed_torrents')]
#[Description('Unbookmark user torrents automatically upon completion')]
class AutoUnbookmarkCompletedTorrents extends Command
{
    /**
     * Execute the console command.
     */
    final public function handle(): void
    {
        $start = now();

        $affected = Bookmark::query()
            ->whereRelation('userSetting', 'unbookmark_torrents_on_completion', '=', true)
            ->whereRelation('history', 'completed_at', '>', now()->subDay())
            ->delete();

        $this->comment($affected.' bookmarks unbookmarked on torrent completion in '.(int) now()->diffInSeconds($start, true).' seconds.');
    }
}
