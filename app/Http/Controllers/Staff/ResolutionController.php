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
use App\Http\Requests\Staff\StoreResolutionRequest;
use App\Http\Requests\Staff\UpdateResolutionRequest;
use App\Models\Resolution;
use Exception;

class ResolutionController extends Controller
{
    /**
     * Display All Resolutions.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.resolution.index', [
            'resolutions' => Resolution::orderBy('position')->get(),
        ]);
    }

    /**
     * Show Resolution Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.resolution.create');
    }

    /**
     * Store A New Resolution.
     */
    public function store(StoreResolutionRequest $request): \Illuminate\Http\RedirectResponse
    {
        Resolution::create($request->validated());

        return to_route('staff.resolutions.index')
            ->with('success', 'Resolution successfully added');
    }

    /**
     * Resolution Edit Form.
     */
    public function edit(Resolution $resolution): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.resolution.edit', [
            'resolution' => $resolution,
        ]);
    }

    /**
     * Edit A Resolution.
     */
    public function update(UpdateResolutionRequest $request, Resolution $resolution): \Illuminate\Http\RedirectResponse
    {
        $resolution->update($request->validated());

        return to_route('staff.resolutions.index')
            ->with('success', 'Resolution successfully modified');
    }

    /**
     * Delete A Resolution.
     *
     * @throws Exception
     */
    public function destroy(Resolution $resolution): \Illuminate\Http\RedirectResponse
    {
        $resolution->delete();

        return to_route('staff.resolutions.index')
            ->with('success', 'Resolution successfully deleted');
    }
}
