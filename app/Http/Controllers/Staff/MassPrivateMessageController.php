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
use App\Http\Requests\Staff\StoreMassPrivateMessageRequest;
use App\Jobs\ProcessMassPM;
use App\Models\Group;
use App\Models\User;

class MassPrivateMessageController extends Controller
{
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.mass_private_message.create', [
            'groups' => Group::orderBy('position')->get()
        ]);
    }

    public function store(StoreMassPrivateMessageRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->validated();

        $userIds = User::whereIntegerInRaw('group_id', $request->group_ids)->pluck('id');

        foreach ($userIds as $userId) {
            dispatch(new ProcessMassPM(User::SYSTEM_USER_ID, $userId, $request->subject, $request->message));
        }

        return to_route('staff.dashboard.index')
            ->with('success', 'Private messages have been queued for processing.');
    }
}
