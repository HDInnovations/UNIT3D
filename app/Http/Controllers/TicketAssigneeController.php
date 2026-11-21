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

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketAssigneeRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\CurrentUser;

class TicketAssigneeController extends Controller
{
    final public function store(StoreTicketAssigneeRequest $request, Ticket $ticket, #[CurrentUser] User $currentUser): \Illuminate\Http\RedirectResponse
    {
        abort_unless($currentUser->group->is_modo, 403);

        $ticket->update([
            'staff_id'   => $request->staffId,
            'staff_read' => false,
        ]);

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.assigned-success'));
    }

    final public function destroy(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $ticket->update([
            'staff_id' => null,
        ]);

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.unassigned-success'));
    }
}
