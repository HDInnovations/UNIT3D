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

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;

final class AutoCloseInactiveTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:auto-close-inactive {--dry-run : Preview actions without persisting changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings inactive help desk tickets after 3 days and closes them after 4 days of user inactivity';

    /**
     * Execute the console command.
     */
    final public function handle(): void
    {
        $dryRun = (bool) $this->option('dry-run');

        Ticket::query()
            ->whereNull('closed_at')
            ->whereNotNull('staff_id')
            ->with(['comments' => fn ($q) => $q->latest()])
            ->each(function (Ticket $ticket) use ($dryRun): void {
                $latestComment = $ticket->comments->first();

                if ($latestComment === null) {
                    return;
                }

                $latestCommentIsFromStaff = $latestComment->user_id === $ticket->staff_id;

                if (! $latestCommentIsFromStaff) {
                    return;
                }

                $staffRepliedAt = $latestComment->created_at;
                $warningThreshold = now()->subDays(3);
                $closureThreshold = now()->subDays(4);

                if ($staffRepliedAt->greaterThan($warningThreshold)) {
                    return;
                }

                $hasWarning = $ticket->comments
                    ->contains(fn (Comment $comment) => $comment->user_id === User::SYSTEM_USER_ID);

                if ($hasWarning && $staffRepliedAt->lessThanOrEqualTo($closureThreshold)) {
                    if (! $dryRun) {
                        $ticket->comments()->create([
                            'user_id' => User::SYSTEM_USER_ID,
                            'content' => 'This ticket has been automatically closed due to prolonged user inactivity.',
                            'anon'    => false,
                        ]);

                        $ticket->update(['closed_at' => now()]);
                    }

                    $this->line(sprintf('[closed] Ticket #%d', $ticket->id));

                    return;
                }

                if (! $hasWarning) {
                    if (! $dryRun) {
                        $ticket->comments()->create([
                            'user_id' => User::SYSTEM_USER_ID,
                            'content' => 'This ticket will be automatically closed in 24 hours if there is no further response from you.',
                            'anon'    => false,
                        ]);
                    }

                    $this->line(sprintf('[pinged] Ticket #%d', $ticket->id));
                }
            }, 100);

        $this->comment('Automated inactive ticket close command complete');
    }
}
