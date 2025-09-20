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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreBonEarningRequest;
use App\Http\Requests\Staff\UpdateBonEarningRequest;
use App\Models\BonEarning;
use Exception;
use Illuminate\Support\Arr;

class BonEarningController extends Controller
{
    /**
     * Display All Bon Earnings.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.bon-earning.index', [
            'bonEarnings' => BonEarning::with('conditions')->orderBy('position')->get(),
        ]);
    }

    /**
     * Show Form For Creating A New Bon Earning.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.bon-earning.create');
    }

    /**
     * Store A Bon Earning.
     */
    public function store(StoreBonEarningRequest $request): \Illuminate\Http\RedirectResponse
    {
        $bonEarning = BonEarning::create($request->validated('bon_earning'));

        $bonEarning->conditions()->upsert($request->validated('conditions', []), ['id']);

        return to_route('staff.bon_earnings.index')
            ->with('success', 'Bon exchange successfully added');
    }

    /**
     * Bon Earning Edit Form.
     */
    public function edit(BonEarning $bonEarning): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.bon-earning.edit', [
            'bonEarning' => $bonEarning->load('conditions'),
        ]);
    }

    /**
     * Update A Bon Earning.
     */
    public function update(UpdateBonEarningRequest $request, BonEarning $bonEarning): \Illuminate\Http\RedirectResponse
    {
        $bonEarning->update($request->validated('bon_earning'));

        $bonEarning->conditions()
            ->whereNotIn('id', Arr::flatten($request->validated('conditions.*.id', [])))
            ->delete();

        $bonEarning->conditions()->upsert($request->validated('conditions', []), ['id']);

        return to_route('staff.bon_earnings.index')
            ->with('success', 'Bon exchange successfully modified');
    }

    /**
     * Destroy A Bon Earning.
     *
     * @throws Exception
     */
    public function destroy(BonEarning $bonEarning): \Illuminate\Http\RedirectResponse
    {
        $bonEarning->delete();

        return to_route('staff.bon_earnings.index')
            ->with('success', 'Bon exchange successfully deleted');
    }
}
