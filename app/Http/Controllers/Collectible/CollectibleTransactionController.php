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

class CollectibleTransactionController extends Controller
{
    /**
     * Buy A Collectible From Store.
     *
     * User buys in stock collectible.
     */
    public function create(Collectible $collectible): \Illuminate\Http\RedirectResponse
    {
        $user = User::withSum('seedingTorrents as seedsize', 'size')
            ->withAvg('history', 'seedtime')
            ->findOrFail(auth()->user()->id);

        $userOwns = $collectible->items()->where('user_id', '=', $user->id)->exists();

        if ($userOwns) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You already own this item.");
        }

        // Evaluate the requirements for this collectible for the current user
        $userAttributes = [
            'uploaded' => [
                'value' => $user->uploaded ?? 0,
                'label' => 'Account Upload'
            ],
            'seedsize' => [
                'value' => $user->seedsize ?? 0,
                'label' => 'Seeding Size'
            ],
            'average_seedtime' => [
                'value' => $user->history_avg_seedtime ?? 0,
                'label' => 'Average Seedtime'
            ],
            'ratio' => [
                'value' => $user->ratio ?? 0,
                'label' => 'Account Ratio'
            ],
            'age' => [
                'value' => $user->created_at->diffInSeconds(now()),
                'label' => 'Account Age'
            ],
        ];

        $userMeetsAllRequirements = true;

        foreach ($collectible->requirements as $req) {
            $attrValue = $userAttributes[$req->operand1]['value'];

            // Determine if the requirement is met
            $requirementMet = match ($req->operator) {
                '>'     => $attrValue > $req->operand2,
                '<'     => $attrValue < $req->operand2,
                '='     => $attrValue == $req->operand2,
                '>='    => $attrValue >= $req->operand2,
                '<='    => $attrValue <= $req->operand2,
                default => false,
            };

            // Update the overall requirement status
            $userMeetsAllRequirements &= $requirementMet;

            // Break early if any requirement is not met
            if (!$userMeetsAllRequirements) {
                break;
            }
        }

        if (! $userMeetsAllRequirements) {
            return to_route('collectibles.show', ['collectible' => $collectible])
                ->withErrors("You do not meet all requirements to buy this item!");
        }

        if ($collectible->price > $user->seedbonus) {
            return back()->withErrors('Not enough BON.');
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
            ->with('success', "Collectible bought");
    }
}
