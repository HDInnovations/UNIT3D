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

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Show all groups.
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();

        return view('group.index', [
            'current'           => Carbon::now(),
            'user'              => $user,
            'user_avg_seedtime' => DB::table('history')->where('user_id', '=', $user->id)->avg('seedtime'),
            'user_account_age'  => (int) Carbon::now()->diffInSeconds($user->created_at, true),
            'user_seed_size'    => $user->seedingTorrents()->sum('size'),
            'user_uploads'      => $user->torrents()->count(),
            'groups'            => Group::query()->orderBy('position')->where('is_modo', '=', 0)->get(),
        ]);
    }
}
