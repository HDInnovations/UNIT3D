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
use App\Models\Resurrection;
use App\Models\History;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Http\Request;

class ResurrectionController extends Controller
{
    /**
     * Show user resurrections.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        return view('user.resurrection.index', ['user' => $user]);
    }

    /**
     * Resurrect A Torrent.
     */
    public function store(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $torrent = Torrent::query()->findOrFail($request->integer('torrent_id'));

        if ($user->id === $torrent->user_id) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors(trans('graveyard.resurrect-failed-own'));
        }

        if ($torrent->seeders !== 0) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('This torrent is not dead.');
        }

        if ($torrent->created_at->gt(now()->subDays(30))) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('This torrent is not older than 30 days.');
        }

        $isPending = Resurrection::query()->whereBelongsTo($torrent)->where('rewarded', '=', 0)->exists();

        if ($isPending) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors(trans('graveyard.resurrect-failed-pending'));
        }

        $history = History::query()->whereBelongsTo($torrent)->whereBelongsTo($user)->first();

        Resurrection::query()->create([
            'user_id'    => $user->id,
            'torrent_id' => $torrent->id,
            'seedtime'   => config('graveyard.time') + ($history?->seedtime ?? 0),
        ]);

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', trans('graveyard.resurrect-complete'));
    }

    /**
     * Cancel A Resurrection.
     */
    public function destroy(Request $request, User $user, Resurrection $resurrection): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        $resurrection->delete();

        return to_route('users.resurrections.index', ['user' => $user])
            ->with('success', trans('graveyard.resurrect-canceled'));
    }
}
