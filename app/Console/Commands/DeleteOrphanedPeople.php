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

use App\Models\TmdbPerson;
use Illuminate\Console\Command;

class DeleteOrphanedPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:delete_orphaned_people';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes people who aren\'t credited';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $start = now();

        $deletedPeople = TmdbPerson::query()->whereDoesntHave('credits')->delete();

        $elapsed = (int) now()->diffInSeconds($start, true);

        $this->info("Deleted {$deletedPeople} people in {$elapsed} seconds");
    }
}
