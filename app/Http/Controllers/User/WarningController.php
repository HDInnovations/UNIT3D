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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warning;
use App\Notifications\WarningCreated;
use App\Notifications\WarningTorrentDeleted;
use App\Notifications\WarningsDeleted;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Carbon;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\WarningControllerTest
 */
class WarningController extends Controller
{
    /**
     * Manually warn a user.
     */
    protected function store(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        Warning::create([
            'user_id'    => $user->id,
            'warned_by'  => $request->user()->id,
            'torrent'    => null,
            'reason'     => $request->string('message'),
            'expires_on' => Carbon::now()->addDays(config('hitrun.expire')),
            'active'     => true,
        ]);

        $user->notify(new WarningCreated($request->string('message')->toString()));

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Warning issued successfully!');
    }

    /**
     * Delete A Warning.
     *
     *
     * @throws Exception
     */
    public function destroy(Request $request, User $user, Warning $warning): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $staff = $request->user();

        $user->notify(new WarningTorrentDeleted($staff, $warning));

        $warning->update([
            'deleted_by' => $staff->id,
        ]);

        $warning->delete();

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Warning was successfully deleted');
    }

    /**
     * Delete All Warnings.
     */
    public function massDestroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $staff = $request->user();

        $user->warnings()->update([
            'deleted_by' => $staff->id,
        ]);

        $user->warnings()->delete();

        $user->notify(new WarningsDeleted($staff));

        return to_route('users.show', ['user' => $user])
            ->with('success', 'All warnings were successfully deleted');
    }

    /**
     * Restore A Soft Deleted Warning.
     */
    public function update(Request $request, User $user, Warning $warning): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $warning->restore();

        return to_route('users.show', ['user' => $user])
            ->with('success', 'Warning was successfully restored');
    }
}
