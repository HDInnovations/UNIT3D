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

class HistoryController extends Controller
{
    /**
     * Show user torrent history.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        return view('user.history.index', [
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
}
