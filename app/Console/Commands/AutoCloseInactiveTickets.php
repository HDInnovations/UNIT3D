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

use App\Models\Ticket;
use App\Notifications\TicketAutoCloseReminder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AutoCloseInactiveTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:close_inactive_tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind users about stale helpdesk tickets and close them if they remain inactive.';

    final public function handle(): void
    {
        $now = now();
        $reminderDays = max(1, (int) config('ticket.auto_close_reminder_days', 3));
        $graceHours = max(1, (int) config('ticket.auto_close_grace_hours', 24));
        $closed = 0;
        $reminded = 0;

        $this->ticketsWithLatestStaffReply($now)
            ->where('reminded_at', '<=', $now->copy()->subHours($graceHours))
            ->chunkById(100, function ($tickets) use (&$closed, $now): void {
                foreach ($tickets as $ticket) {
                    $ticket->update([
                        'closed_at' => $now,
                    ]);

                    ++$closed;
                }
            });

        $this->ticketsWithLatestStaffReply($now->copy()->subDays($reminderDays))
            ->whereNull('reminded_at')
            ->with('user')
            ->chunkById(100, function ($tickets) use (&$reminded, $graceHours, $now): void {
                foreach ($tickets as $ticket) {
                    $ticket->update([
                        'reminded_at' => $now,
                        'user_read'   => false,
                    ]);

                    $ticket->user->notify(new TicketAutoCloseReminder($ticket, $graceHours));

                    ++$reminded;
                }
            });

        $this->comment("Automated inactive ticket command complete: {$reminded} reminded, {$closed} closed.");
    }

    /**
     * @return Builder<Ticket>
     */
    private function ticketsWithLatestStaffReply(Carbon $latestCommentCutoff): Builder
    {
        return Ticket::query()
            ->whereNull('closed_at')
            ->whereHas(
                'latestComment',
                fn (Builder $query) => $query->where('created_at', '<=', $latestCommentCutoff)
            )
            ->whereHas(
                'latestComment.user.group',
                fn (Builder $query) => $query->where('is_modo', '=', true)
            );
    }
}
