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

use App\Http\Requests\Staff\StoreDonationPackageRequest;
use App\Http\Requests\Staff\UpdateDonationPackageRequest;
use Illuminate\Http\Request;
use App\Models\DonationPackage;
use App\Http\Controllers\Controller;

class DonationPackageController extends Controller
{
    /**
     * Show All Donation Packages.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.donation-package.index', ['packages' => DonationPackage::orderBy('position')->paginate(25)]);
    }

    /**
     * Donation Package Add Form.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.donation-package.create');
    }

    /**
     * Store A Donation Package.
     */
    public function store(StoreDonationPackageRequest $request): \Illuminate\Http\RedirectResponse
    {
        DonationPackage::create($request->validated());

        return redirect()->route('staff.packages.index')
            ->with('success', 'Donation package added successfully!');
    }

    /**
     * Donation Package Edit Form.
     */
    public function edit(DonationPackage $package): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.donation-package.edit', ['package' => $package]);
    }

    /**
     * Edit A Donation Package.
     */
    public function update(UpdateDonationPackageRequest $request, DonationPackage $package): \Illuminate\Http\RedirectResponse
    {
        $package->update($request->validated());

        return redirect()->route('staff.packages.index')
            ->with('success', 'Donation package edited successfully!');
    }

    /**
     * Delete A Donation Package.
     */
    public function destroy(Request $request, DonationPackage $package): \Illuminate\Http\RedirectResponse
    {
        $package->delete();

        return redirect()->route('staff.packages.index')
            ->with('success', 'Donation package deleted successfully!');
    }
}
