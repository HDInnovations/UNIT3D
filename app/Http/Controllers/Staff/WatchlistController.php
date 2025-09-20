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
use App\Http\Requests\Staff\StoreWatchedUserRequest;
use App\Models\Watchlist;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\WatchlistControllerTest
 */
class WatchlistController extends Controller
{
    /**
     * Watchlist.
     */
    final public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.watchlist.index');
    }

    /**
     * Store A New Watched User.
     */
    final public function store(StoreWatchedUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        Watchlist::create(['staff_id' => $request->user()->id] + $request->validated());

        return back()->with('success', 'User successfully being watched');
    }

    /**
     * Delete A Watched User.
     *
     * @throws Exception
     */
    final public function destroy(Watchlist $watchlist): \Illuminate\Http\RedirectResponse
    {
        $watchlist->delete();

        return back()->with('success', 'Successfully stopped watching user');
    }
}
