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
use App\Http\Requests\Staff\StoreGiveawayPrizeRequest;
use App\Http\Requests\Staff\UpdateGiveawayPrizeRequest;
use App\Models\Giveaway;
use App\Models\GiveawayPrize;

class GiveawayPrizeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGiveawayPrizeRequest $request, Giveaway $giveaway): \Illuminate\Http\RedirectResponse
    {
        $giveaway->prizes()->create($request->validated());

        return to_route('staff.giveaways.edit', [
            'giveaway' => $giveaway
        ])
            ->with('success', 'Prize added to giveaway.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGiveawayPrizeRequest $request, Giveaway $giveaway, GiveawayPrize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->update($request->validated());

        return to_route('staff.giveaways.edit', [
            'giveaway' => $giveaway
        ])
            ->with('success', 'Prize updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Giveaway $giveaway, GiveawayPrize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->delete();

        return to_route('staff.giveaways.edit', [
            'giveaway' => $giveaway
        ])
            ->with('success', 'Prize removed from giveaway.');
    }
}
