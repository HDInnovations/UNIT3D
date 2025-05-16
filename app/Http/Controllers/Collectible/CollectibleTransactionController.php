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
use App\Models\Collectible;
use App\Models\CollectibleTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CollectibleTransactionController extends Controller
{
    /**
     * Buy A Collectible From Store.
     *
     * User buys in stock collectible.
     */
    public function create(Collectible $collectible): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $userAvgSeedtime = DB::table('history')->where('user_id', '=', $user->id)->avg('seedtime');
        $userSeedSize = $user->seedingTorrents()->sum('size');

        $userOwns = $collectible->items()->where('user_id', '=', $user->id)->exists();

        if ($userOwns) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You already own this item.");
        }

        if ($collectible->price > $user->seedbonus) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors('Not enough BON.');
        }

        $requirements = $collectible->requirements;
        $userMeetsAllRequirements = (
            ($requirements->min_uploaded === null || $user->uploaded >= $requirements->min_uploaded)
            && ($requirements->min_seedsize === null || $userSeedSize >= $requirements->min_seedsize)
                && ($requirements->min_avg_seedtime === null || $userAvgSeedtime >= $requirements->min_avg_seedtime)
            && ($requirements->min_ratio === null || $user->ratio >= $requirements->min_ratio)
            && ($requirements->min_age === null || $user->created_at->diffInSeconds(now()) >= $requirements->min_age)
        );

        if (! $userMeetsAllRequirements) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You do not meet all requirements to buy this item!");
        }

        // Get the first available collectible item that is not owned by a user
        $collectibleItem = $collectible->items()->whereNull('user_id')->first();

        $transaction = CollectibleTransaction::create([
            'collectible_id' => $collectible->id,
            'seller_id'      => User::SYSTEM_USER_ID,
            'buyer_id'       => $user->id,
            'price'          => $collectible->price,
        ]);

        $collectibleItem->update([
            'user_id' => $user->id,
        ]);

        $user->decrement('seedbonus', $collectibleItem->collectible->price);

        return to_route('collectibles.show', ['collectible' => $collectible])
            ->with('success', "Collectible bought.");
    }
}
