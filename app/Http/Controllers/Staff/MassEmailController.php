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
use App\Http\Requests\Staff\MassEmailRequest;
use App\Jobs\SendMassEmail;
use App\Models\Group;
use App\Models\User;

class MassEmailController extends Controller
{
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.mass-email.create', ['groups' => Group::query()->orderBy('position')->get()]);
    }

    public function store(MassEmailRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->validated();

        $users = User::query()->whereIntegerInRaw('group_id', $request->groups)->get();

        foreach ($users as $user) {
            dispatch(new SendMassEmail($user, $request->subject, $request->message));
        }

        return to_route('staff.dashboard.index')
            ->with('success', 'Emails have been queued for processing to avoid spamming the mail server');
    }
}
