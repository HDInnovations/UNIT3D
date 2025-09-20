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

use App\Http\Requests\Staff\StoreDonationGatewayRequest;
use App\Http\Requests\Staff\UpdateDonationGatewayRequest;
use Illuminate\Http\Request;
use App\Models\DonationGateway;
use App\Http\Controllers\Controller;

class DonationGatewayController extends Controller
{
    /**
     * Get All Donation Gateways.
     */
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($request->user()->group->is_owner, 403);

        return view('Staff.donation-gateway.index', ['gateways' => DonationGateway::orderBy('position')->paginate(25)]);
    }

    /**
     * Create A Donation Gateway.
     */
    public function create(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($request->user()->group->is_owner, 403);

        return view('Staff.donation-gateway.create');
    }

    /**
     * Store A Donation Gateway.
     */
    public function store(StoreDonationGatewayRequest $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_owner, 403);

        DonationGateway::create($request->validated());

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway added successfully!');
    }

    /**
     * Edit A Donation Gateway.
     */
    public function edit(Request $request, DonationGateway $gateway): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($request->user()->group->is_owner, 403);

        return view('Staff.donation-gateway.edit', ['gateway' => $gateway]);
    }

    /**
     * Update A Donation Gateway.
     */
    public function update(UpdateDonationGatewayRequest $request, DonationGateway $gateway): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_owner, 403);

        $gateway->update($request->validated());

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway edited successfully!');
    }

    /**
     * Destroy A Donation Gateway.
     */
    public function destroy(Request $request, DonationGateway $gateway): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_owner, 403);

        $gateway->delete();

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway deleted successfully!');
    }
}
