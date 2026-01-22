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

use App\Http\Requests\StoreTicketNoteRequest;
use App\Models\Ticket;
use App\Models\TicketNote;
use Illuminate\Http\Request;

class TicketNoteController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    final public function store(StoreTicketNoteRequest $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->group->is_modo, 403);

        TicketNote::query()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id] + $request->validated());

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.note-create-success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    final public function destroy(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $ticket->notes()->delete();

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.note-destroy-success'));
    }
}
