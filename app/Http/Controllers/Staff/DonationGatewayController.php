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
use App\Models\User;
use App\Models\DonationGateway;
use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\CurrentUser;

class DonationGatewayController extends Controller
{
    /**
     * Get All Donation Gateways.
     */
    public function index(#[CurrentUser] User $user): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($user->group->is_owner, 403);

        return view('Staff.donation-gateway.index', ['gateways' => DonationGateway::query()->orderBy('position')->paginate(25)]);
    }

    /**
     * Create A Donation Gateway.
     */
    public function create(#[CurrentUser] User $user): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($user->group->is_owner, 403);

        return view('Staff.donation-gateway.create');
    }

    /**
     * Store A Donation Gateway.
     */
    public function store(StoreDonationGatewayRequest $request, #[CurrentUser] User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($user->group->is_owner, 403);

        DonationGateway::query()->create($request->toArray());

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway added successfully!');
    }

    /**
     * Edit A Donation Gateway.
     */
    public function edit(DonationGateway $gateway, #[CurrentUser] User $user): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        abort_unless($user->group->is_owner, 403);

        return view('Staff.donation-gateway.edit', ['gateway' => $gateway]);
    }

    /**
     * Update A Donation Gateway.
     */
    public function update(UpdateDonationGatewayRequest $request, DonationGateway $gateway, #[CurrentUser] User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($user->group->is_owner, 403);

        $gateway->update($request->toArray());

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway edited successfully!');
    }

    /**
     * Destroy A Donation Gateway.
     */
    public function destroy(DonationGateway $gateway, #[CurrentUser] User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($user->group->is_owner, 403);

        $gateway->delete();

        return redirect()->route('staff.gateways.index')
            ->with('success', 'Donation gateway deleted successfully!');
    }
}
