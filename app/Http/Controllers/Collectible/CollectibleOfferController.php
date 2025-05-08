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
use App\Http\Requests\StoreCollectibleOfferRequest;
use App\Models\Collectible;
use App\Models\CollectibleOffer;
use App\Models\CollectibleTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectibleOfferController extends Controller
{
    /**
     * Create A Collectible Offer.
     *
     * User creates a new offer.
     */
    public function create(Collectible $collectible): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('collectible.offer.create', [
            'collectibleItem' => $collectible->items()->where('user_id', '=', auth()->user()->id)->first(),
        ]);
    }

    /**
     * Store A New Collectible Offer.
     *
     * Store User creates a new offer.
     */
    public function store(Collectible $collectible, StoreCollectibleOfferRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->validated('offer');

        $user = auth()->user();

        $userOwns = $collectible->items()->where('user_id', '=', $user->id)->exists();
        $userIsSelling = $collectible->offers()->where('user_id', $user->id)->whereNull('filled_at')->exists();

        if (! $userOwns || $collectible->in_stock || $userIsSelling) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You can not sell this item. Either you do not own it, its in stock at the regular market or you are already selling it.");
        }

        if ($request->sell_price > $collectible->price * 1.2) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("The highest selling price for this item is 1.2 times its original price.");
        }

        $offer = CollectibleOffer::create([
            'collectible_id' => $collectible->id,
            'user_id'        => $user->id,
            'price'          => $request->sell_price,
        ]);

        return to_route('collectibles.show', ['collectible' => $collectible])
            ->with('success', "Offer created.");
    }

    /**
     * Store An Collectible Offer Accept.
     *
     * User accepts an existing offer.
     */
    public function update(CollectibleOffer $collectibleOffer): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        $collectible = $collectibleOffer->collectible;
        $userOwns = $collectible->items()->where('user_id', '=', $user->id)->exists();

        if ($userOwns) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You already own this item.");
        }

        if ($user->id === $collectibleOffer->seller_id) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You can not accept your own offer.");
        }

        if ($collectibleOffer->price > $user->seedbonus) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("Not enough BON.");
        }

        // Get the offered item
        $collectibleItem = $collectible->items()->where('user_id', '=', $collectibleOffer->seller->id)->first();

        DB::transaction(static function () use ($collectible, $collectibleItem, $collectibleOffer, $user): void {
            $transaction = CollectibleTransaction::create([
                'collectible_id' => $collectible->id,
                'seller_id'      => $collectibleOffer->seller->id,
                'buyer_id'       => $user->id,
                'price'          => $collectibleOffer->price,
            ]);

            $collectibleItem->update([
                'user_id' => $user->id,
            ]);

            $collectibleOffer->update([
                'filled_at' => now(),
            ]);

            User::whereKey($transaction->seller_id)->increment('seedbonus', (float) $transaction->price);
            User::whereKey($transaction->buyer_id)->decrement('seedbonus', (float) $transaction->price);
        });

        return to_route('collectibles.show', ['collectible' => $collectible])
            ->with('success', "Offer accepted.");
    }

    /**
     * Destroy An Collectible Offer.
     *
     * User deletes his existing offer.
     */
    public function destroy(Request $request, CollectibleOffer $collectibleOffer): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($collectibleOffer->seller) || $request->user()->group->is_modo, 403);

        $collectibleOffer->delete();

        return to_route('collectibles.show', ['collectible' => $collectibleOffer->collectible])
            ->with('success', 'Offer deleted.');
    }
}
