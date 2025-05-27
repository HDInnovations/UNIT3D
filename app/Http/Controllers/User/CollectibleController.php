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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CollectibleCategory;
use App\Models\Collectible;
use App\Models\User;
use Illuminate\Http\Request;

class CollectibleController extends Controller
{
    /**
     * Display User Collectibles.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->is($user), 403);

        return view('user.collectible.index', [
            'collectibleCategories'  => CollectibleCategory::orderBy('position')->get(),
            'collectiblesByCategory' => Collectible::whereHas('items', function ($query) use ($user): void {
                $query->where('user_id', '=', $user->id);
            })
                ->with('category')
                ->get()
                ->groupBy('category_id'),
            'user' => $user,
        ]);
    }
}
