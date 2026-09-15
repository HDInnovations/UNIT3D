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
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('auto:delete_orphaned_people')]
#[Description('Deletes people who aren\'t credited')]
class DeleteOrphanedPeople extends Command
{
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
