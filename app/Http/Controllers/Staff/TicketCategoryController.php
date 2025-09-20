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
use App\Http\Requests\Staff\StoreTicketCategoryRequest;
use App\Http\Requests\Staff\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\TicketCategoryControllerTest
 */
class TicketCategoryController extends Controller
{
    /**
     * Display All Ticket Categories.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-category.index', [
            'ticketCategories' => TicketCategory::orderBy('position')->get(),
        ]);
    }

    /**
     * Show Ticket Category Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-category.create');
    }

    /**
     * Store A New Ticket Category.
     */
    public function store(StoreTicketCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        TicketCategory::create($request->validated());

        return to_route('staff.ticket_categories.index')
            ->with('success', 'Ticket category successfully added');
    }

    /**
     * Ticket Category Edit Form.
     */
    public function edit(TicketCategory $ticketCategory): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.ticket-category.edit', [
            'ticketCategory' => $ticketCategory,
        ]);
    }

    /**
     * Edit A Ticket Category.
     */
    public function update(UpdateTicketCategoryRequest $request, TicketCategory $ticketCategory): \Illuminate\Http\RedirectResponse
    {
        $ticketCategory->update($request->validated());

        return to_route('staff.ticket_categories.index')
            ->with('success', 'Ticket category successfully modified');
    }

    /**
     * Delete A Ticket Category.
     *
     * @throws Exception
     */
    public function destroy(TicketCategory $ticketCategory): \Illuminate\Http\RedirectResponse
    {
        $ticketCategory->delete();

        return to_route('staff.ticket_categories.index')
            ->with('success', 'Ticket category successfully deleted');
    }
}
