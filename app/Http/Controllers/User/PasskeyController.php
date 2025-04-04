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
use App\Notifications\PasskeyReset;
use App\Services\Unit3dAnnounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasskeyController extends Controller
{
    /**
     * Display a users passkeys.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        return view('user.passkey.index', [
            'user'     => $user,
            'passkeys' => $user->passkeys()->latest()->get(),
        ]);
    }

    /**
     * Update user passkey.
     */
    protected function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user) || $request->user()->group->is_modo, 403);

        $changedByStaff = $request->user()->isNot($user);

        abort_if($changedByStaff && !$request->user()->group->is_owner && $request->user()->group->level <= $user->group->level, 403);

        cache()->forget('user:'.$user->passkey);

        $newPasskey = md5(random_bytes(60).$user->password);

        Unit3dAnnounce::addUser($user, $newPasskey);

        DB::transaction(static function () use ($user, $changedByStaff, $newPasskey): void {
            $user->passkeys()->latest()->first()?->update(['deleted_at' => now()]);

            $user->update([
                'passkey' => $newPasskey,
            ]);

            $user->passkeys()->create(['content' => $user->passkey]);

            if ($changedByStaff) {
                $user->notify(new PasskeyReset());
            }
        }, 5);

        return to_route('users.passkeys.index', ['user' => $user])
            ->with('success', 'Your passkey was changed successfully.');
    }
}
