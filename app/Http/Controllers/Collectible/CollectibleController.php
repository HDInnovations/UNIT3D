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
        $user = User::withSum('seedingTorrents as seedsize', 'size')
            ->withAvg('history', 'seedtime')
            ->findOrFail(auth()->user()->id);

        $collectible->load('requirements');

        // User attributes
        $userAttributes = [
            'uploaded' => [
                'label' => __('common.account').' '.__('common.upload')
            ],
            'seedsize' => [
                'label' => __('torrent.seedsize')
            ],
            'average_seedtime' => [
                'label' => 'Average '.__('torrent.seedtime')
            ],
            'ratio' => [
                'label' => __('common.account').' '.__('common.ratio')
            ],
            'age' => [
                'label' => __('common.account').' '.__('torrent.age')
            ],
        ];

        // Evaluate the requirements for this collectible for the current user
        $requirementsResults = [];
        $userMeetsAllRequirements = true;

        foreach ($collectible->requirements as $req) {
            $attrValue = match ($req->operand1) {
                'uploaded'         => $uploaded ??= $user->uploaded ?? 0,
                'seedsize'         => $seedsize ??= $user->seedsize ?? 0,
                'average_seedtime' => $avgSeedtime ??= $user->history_avg_seedtime ?? 0,
                'ratio'            => $ratio ??= $user->ratio ?? 0,
                'age'              => $age ??= $user->created_at->diffInSeconds(now()),
            };

            // Determine if the requirement is met
            $meetsRequirement = match ($req->operator) {
                '>'  => $attrValue > $req->operand2,
                '<'  => $attrValue < $req->operand2,
                '='  => $attrValue == $req->operand2,
                '!=' => $attrValue != $req->operand2,
                '>=' => $attrValue >= $req->operand2,
                '<=' => $attrValue <= $req->operand2,
            };

            // Collect individual results per requirement for the view
            $requirementsResults[] = [
                'requirement' => $req,
                'meets'       => $meetsRequirement,
                'label'       => $userAttributes[$req->operand1]['label']
            ];

            // Update overall status
            $userMeetsAllRequirements &= $meetsRequirement;
        }

        return view('collectible.show', [
            'user'                     => $user,
            'collectible'              => $collectible,
            'userOwns'                 => $collectible->items()->where('user_id', '=', $user->id)->exists(),
            'transactions'             => $collectible->transactions()->latest()->take(20)->get(),
            'offers'                   => $collectible->offers()->whereNull('filled_at')->get(),
            'userIsSelling'            => $collectible->offers()->where('user_id', $user->id)->whereNull('filled_at')->exists(),
            'userTransaction'          => $collectible->transactions()->where('buyer_id', $user->id)->latest()->first(),
            'availableCount'           => $collectible->items()->whereNull('user_id')->count(),
            'userAttributes'           => $userAttributes,
            'requirementsResults'      => $requirementsResults,
            'userMeetsAllRequirements' => $userMeetsAllRequirements,
        ]);
    }
}
