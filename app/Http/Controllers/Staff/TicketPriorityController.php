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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreTicketPriorityRequest;
use App\Http\Requests\Staff\UpdateTicketPriorityRequest;
use App\Models\TicketPriority;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\TicketPriorityControllerTest
 */
class TicketPriorityController extends Controller
{
    /**
     * Display All Ticket Priorities.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-priority.index', [
            'ticketPriorities' => TicketPriority::orderBy('position')->get(),
        ]);
    }

    /**
     * Show Ticket Priority Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-priority.create');
    }

    /**
     * Store A New Ticket Priority.
     */
    public function store(StoreTicketPriorityRequest $request): \Illuminate\Http\RedirectResponse
    {
        TicketPriority::create($request->validated());

        return to_route('staff.ticket_priorities.index')
            ->with('success', 'Ticket priority successfully added');
    }

    /**
     * Ticket Priority Edit Form.
     */
    public function edit(TicketPriority $ticketPriority): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-priority.edit', [
            'ticketPriority' => $ticketPriority,
        ]);
    }

    /**
     * Edit A Ticket Priority.
     */
    public function update(UpdateTicketPriorityRequest $request, TicketPriority $ticketPriority): \Illuminate\Http\RedirectResponse
    {
        $ticketPriority->update($request->validated());

        return to_route('staff.ticket_priorities.index')
            ->with('success', 'Ticket priority successfully modified');
    }

    /**
     * Delete A Ticket Priority.
     *
     * @throws Exception
     */
    public function destroy(TicketPriority $ticketPriority): \Illuminate\Http\RedirectResponse
    {
        $ticketPriority->delete();

        return to_route('staff.ticket_priorities.index')
            ->with('success', 'Ticket priority successfully deleted');
    }
}
