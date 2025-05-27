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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Collectible;

use App\Http\Controllers\Controller;
use App\Models\CollectibleCategory;
use App\Models\Collectible;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CollectibleController extends Controller
{
    /**
     * Display All Collectibles.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('collectible.index', [
            'collectibles' => CollectibleCategory::with('collectibles')->has('collectibles')->orderBy('position')->get(),
        ]);
    }

    /**
     * Display A Collectible.
     */
    public function show(Collectible $collectible): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = User::withAvg('history as avg_seedtime', 'seedtime')
            ->findOrFail(auth()->user()->id);
        $userAvgSeedtime = DB::table('history')->where('user_id', '=', $user->id)->avg('seedtime');
        $userSeedSize = $user->seedingTorrents()->sum('size');

        $requirements = $collectible->requirements;

        return view('collectible.show', [
            'user'                     => $user,
            'userAvgSeedtime'          => $userAvgSeedtime,
            'userSeedSize'             => $userSeedSize,
            'collectible'              => $collectible,
            'userOwns'                 => $collectible->items()->where('user_id', '=', $user->id)->exists(),
            'transactions'             => $collectible->transactions()->latest()->take(50)->get(),
            'offers'                   => $collectible->offers()->whereNull('filled_at')->get(),
            'userIsSelling'            => $collectible->offers()->where('user_id', $user->id)->whereNull('filled_at')->exists(),
            'userTransaction'          => $collectible->transactions()->where('buyer_id', $user->id)->latest()->first(),
            'availableCount'           => $collectible->items()->whereNull('user_id')->count(),
            'requirements'             => $requirements,
            'userMeetsAllRequirements' => (
                ($requirements->min_uploaded === null || $user->uploaded >= $requirements->min_uploaded)
                && ($requirements->min_seedsize === null || $userSeedSize >= $requirements->min_seedsize)
                && ($requirements->min_avg_seedtime === null || $userAvgSeedtime >= $requirements->min_avg_seedtime)
                && ($requirements->min_ratio === null || $user->ratio >= $requirements->min_ratio)
                && ($requirements->min_age === null || $user->created_at->diffInSeconds(now()) >= $requirements->min_age)
            ),
        ]);
    }
}
