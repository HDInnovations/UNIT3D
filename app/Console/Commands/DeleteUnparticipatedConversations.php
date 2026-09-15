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

use App\Models\Conversation;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('auto:delete_unparticipated_conversations')]
#[Description('Deletes conversation where all users have deleted their participation')]
class DeleteUnparticipatedConversations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $start = now();

        $deletedParticipants = Participant::query()->where('user_id', '=', User::SYSTEM_USER_ID)->delete();

        $deletedConversations = Conversation::query()->whereDoesntHave('participants')->delete();

        $elapsed = (int) now()->diffInSeconds($start, true);

        $this->info("Deleted {$deletedParticipants} participants and {$deletedConversations} conversations in {$elapsed} seconds");
    }
}
