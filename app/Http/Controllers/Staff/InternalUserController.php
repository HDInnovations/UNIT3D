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
use App\Http\Requests\Staff\StoreInternalUserRequest;
use App\Http\Requests\Staff\UpdateInternalUserRequest;
use App\Models\InternalUser;
use App\Models\User;

class InternalUserController extends Controller
{
    public function store(StoreInternalUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        InternalUser::query()->create([
            'user_id'     => User::query()->where('username', '=', $request->string('username'))->value('id'),
            'internal_id' => $request->integer('internal_id'),
            'position'    => $request->integer('position'),
        ]);

        return to_route('staff.internals.edit', [
            'internal' => $request->integer('internal_id')
        ])
            ->with('success', 'User added to group.');
    }

    public function update(UpdateInternalUserRequest $request, InternalUser $internalUser): \Illuminate\Http\RedirectResponse
    {
        $internalUser->update($request->validated());

        return to_route('staff.internals.edit', [
            'internal' => $internalUser->internal_id
        ])
            ->with('success', 'User updated.');
    }

    public function destroy(InternalUser $internalUser): \Illuminate\Http\RedirectResponse
    {
        $internalUser->delete();

        return to_route('staff.internals.edit', [
            'internal' => $internalUser->internal_id
        ])
            ->with('success', 'User removed from group.');
    }
}
