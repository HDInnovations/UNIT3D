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

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    final public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('ticket.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    final public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('ticket.create', [
            'categories' => TicketCategory::query()->orderBy('position')->get(),
            'priorities' => TicketPriority::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    final public function store(StoreTicketRequest $request): \Illuminate\Http\RedirectResponse
    {
        $ticket = Ticket::query()->create(['user_id' => $request->user()->id] + $request->validated());

        if ($request->hasFile('attachments')) {
            TicketAttachmentController::storeTicketAttachments($request, $ticket, $request->user());
        }

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.created-success'));
    }

    /**
     * Display the specified resource.
     */
    final public function show(Request $request, Ticket $ticket): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $ticket->user_id, 403);

        if ($request->user()->id === $ticket->user_id) {
            $ticket->user_read = true;
        }

        if ($request->user()->id === $ticket->staff_id) {
            $ticket->staff_read = true;
        }

        $ticket->save();

        return view('ticket.show', [
            'user'            => $request->user(),
            'ticket'          => $ticket->load('comments', 'notes'),
            'pastUserTickets' => Ticket::query()
                ->where('user_id', '=', $ticket->user_id)
                ->where('id', '!=', $ticket->id)
                ->get(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    final public function destroy(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $ticket->notes()->delete();
        $ticket->comments()->delete();
        $ticket->attachments()->delete();
        $ticket->delete();

        return to_route('tickets.index')
            ->with('success', trans('ticket.deleted-success'));
    }

    final public function close(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $ticket->user_id, 403);

        $ticket->update([
            'closed_at' => now(),
        ]);

        return to_route('tickets.index')
            ->with('success', trans('ticket.closed-success'));
    }

    final public function reopen(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $ticket->user_id, 403);

        $ticket->update([
            'closed_at' => null,
        ]);

        return to_route('tickets.show', ['ticket' => $ticket])
            ->with('success', trans('ticket.reopened-success'));
    }
}
