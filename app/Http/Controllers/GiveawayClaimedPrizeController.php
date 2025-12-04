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

namespace App\Http\Controllers;

use App\Models\GiveawayClaimedPrize;
use App\Models\Giveaway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiveawayClaimedPrizeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Giveaway $giveaway): \Illuminate\Http\RedirectResponse
    {
        if (!$giveaway->active) {
            return back()->withErrors('Giveaway is not active.');
        }

        $isAvailable = now()->isBetween($giveaway->starts_at->startOfDay(), $giveaway->ends_at->endOfDay());

        if (!$isAvailable) {
            return back()->withErrors('Prizes are not currently available.');
        }

        return DB::transaction(function () use ($request, $giveaway) {
            $prizeExists = GiveawayClaimedPrize::query()
                ->whereBelongsTo($request->user())
                ->whereBelongsTo($giveaway)
                ->where('created_at', '>', now()->startOfDay())
                ->exists();

            if ($prizeExists) {
                return back()->withErrors('You have already claimed your daily prize. Check back tomorrow!');
            }

            $prizes = $giveaway->prizes;
            $randomNumber = random_int(1, $prizes->sum('weight') ?: 1);
            $selectedPrize = null;

            foreach ($prizes as $prize) {
                if ($randomNumber <= $prize->weight) {
                    $selectedPrize = $prize;

                    break;
                }

                $randomNumber -= $prize->weight;
            }

            $bon_won = 0;
            $fl_tokens_won = 0;

            switch ($selectedPrize?->type) {
                case 'bon':
                    $bon_won = random_int($selectedPrize->min, $selectedPrize->max);
                    $request->user()->increment('seedbonus', $bon_won);

                    break;
                case 'fl_tokens':
                    $fl_tokens_won = random_int($selectedPrize->min, $selectedPrize->max);
                    $request->user()->increment('fl_tokens', $fl_tokens_won);

                    break;
            }

            GiveawayClaimedPrize::create([
                'user_id'     => $request->user()->id,
                'giveaway_id' => $giveaway->id,
                'bon'         => $bon_won,
                'fl_tokens'   => $fl_tokens_won,
            ]);

            return to_route('giveaways.show', ['giveaway' => $giveaway])->with('success', 'Congrats! You have won a prize!');
        });
    }
}
