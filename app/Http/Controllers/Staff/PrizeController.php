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
use App\Http\Requests\Staff\StorePrizeRequest;
use App\Http\Requests\Staff\UpdatePrizeRequest;
use App\Models\Event;
use App\Models\Prize;

class PrizeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrizeRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $event->prizes()->create($request->validated());

        return to_route('staff.events.edit', [
            'event' => $event
        ])
            ->with('success', __('application-messages.flash.prize-added'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrizeRequest $request, Event $event, Prize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->update($request->validated());

        return to_route('staff.events.edit', [
            'event' => $event
        ])
            ->with('success', __('application-messages.flash.prize-updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Prize $prize): \Illuminate\Http\RedirectResponse
    {
        $prize->delete();

        return to_route('staff.events.edit', [
            'event' => $event
        ])
            ->with('success', __('application-messages.flash.prize-removed'));
    }
}
