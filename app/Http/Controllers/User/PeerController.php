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
use App\Models\History;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeerController extends Controller
{
    /**
     * Show user peers.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        return view('user.peer.index', [
            'user'    => $user,
            'history' => History::query()
                ->withTrashed()
                ->where('user_id', '=', $user->id)
                ->where('created_at', '>', $user->created_at)
                ->selectRaw('sum(actual_uploaded) as upload')
                ->selectRaw('sum(uploaded) as credited_upload')
                ->selectRaw('sum(actual_downloaded) as download')
                ->selectRaw('sum(downloaded) as credited_download')
                ->first(),
        ]);
    }

    /**
     * Delete user peers.
     */
    public function massDestroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        if (config('announce.external_tracker.is_enabled')) {
            return redirect()->back()->withErrors("The external tracker doesn't support flushing peers.");
        }

        // Check if User can flush
        if ($request->user()->own_flushes <= 0) {
            return redirect()->back()->withErrors('You can only flush twice a day!');
        }

        // Only peers older than 70 minutes are allowed to be flushed otherwise users could use this to exploit leech slots
        $cutoff = now()->subMinutes(70);

        $user->peers()
            ->where('updated_at', '<', $cutoff)
            ->delete();

        $user->history()
            ->where('active', '=', 1)
            ->where('updated_at', '<', $cutoff)
            ->update([
                'active'     => 0,
                'updated_at' => DB::raw('updated_at'),
            ]);

        $user->decrement('own_flushes');

        return redirect()->back()->with('success', 'All peers last announced from the client over 70 minutes ago have been flushed successfully!');
    }
}
