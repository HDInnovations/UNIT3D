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

use App\Enums\ModerationStatus;
use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\DonationGateway;
use App\Models\DonationPackage;

class DonationController extends Controller
{
    /**
     * Display Donation Page.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $packages = DonationPackage::where('is_active', '=', true)->orderBy('position')->get();
        $gateways = DonationGateway::where('is_active', '=', true)->orderBy('position')->get();

        return view('donation.index', ['packages' => $packages, 'gateways' => $gateways]);
    }

    /**
     * Store A Donation.
     */
    public function store(StoreDonationRequest $request)
    {
        Donation::create([
            'status'      => ModerationStatus::PENDING,
            'package_id'  => $request->package_id,
            'user_id'     => auth()->user()->id,
            'transaction' => $request->transaction,
        ]);

        return redirect()->route('donations.index')
            ->with('success', 'Thank you for supporting us! Please allow for up to 48 hours for staff to confirm the transaction.');
    }
}
