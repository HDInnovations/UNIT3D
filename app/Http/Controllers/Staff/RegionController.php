<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreRegionRequest;
use App\Http\Requests\Staff\UpdateRegionRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Exception;

class RegionController extends Controller
{
    /**
     * Display All Regions.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.region.index', [
            'regions' => Region::orderBy('position')->get(),
        ]);
    }

    /**
     * Show Region Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.region.create');
    }

    /**
     * Store A New Region.
     */
    public function store(StoreRegionRequest $request): \Illuminate\Http\RedirectResponse
    {
        Region::create($request->validated());

        return to_route('staff.regions.index')
            ->withSuccess('Region Successfully Added');
    }

    /**
     * Region Edit Form.
     */
    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.region.edit', [
            'region' => Region::findOrFail($id),
        ]);
    }

    /**
     * Edit A Region.
     */
    public function update(UpdateRegionRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        Region::findOrFail($id)->update($request->validated());

        return to_route('staff.regions.index')
            ->withSuccess('Region Successfully Modified');
    }

    /**
     * Delete A Region.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $region = Region::findOrFail($id);
        $region->torrents()->update($request->validated());
        $region->delete();

        return to_route('staff.regions.index')
            ->withSuccess('Region Successfully Deleted');
    }
}
