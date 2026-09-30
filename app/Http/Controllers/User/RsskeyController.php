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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RsskeyReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RsskeyController extends Controller
{
    /**
     * Update user rsskey.
     */
    protected function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        $changedByStaff = $request->user()->isNot($user);

        abort_if($changedByStaff && !$request->user()->group->is_owner && $request->user()->group->level <= $user->group->level, 403);

        DB::transaction(function () use ($user, $changedByStaff): void {
            $user->rsskeys()->latest()->first()?->update(['deleted_at' => now()]);

            $user->update([
                'rsskey' => md5(random_bytes(60).$user->password),
            ]);

            $user->rsskeys()->create(['content' => $user->rsskey]);

            if ($changedByStaff) {
                $user->notify(new RsskeyReset());
            }
        });

        return to_route('users.rsskeys.index', ['user' => $user])
            ->with('success', __('application-messages.flash.rsskey-changed'));
    }

    /**
     * Edit user rsskey.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.rsskey.index', [
            'user'    => $user,
            'rsskeys' => $user->rsskeys()->latest()->get(),
        ]);
    }
}
