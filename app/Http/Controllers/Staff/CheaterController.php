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
use App\Models\User;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\CheaterControllerTest
 */
class CheaterController extends Controller
{
    /**
     * Possible Ghost Leech Cheaters.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.cheater.index', [
            'cheaters' => User::query()
                ->whereHas('history', function ($query): void {
                    $query->where('seeder', '=', 0);
                    $query->where('active', '=', 0);
                    $query->where('seedtime', '=', 0);
                    $query->where('actual_downloaded', '=', 0);
                    $query->where('actual_uploaded', '=', 0);
                    $query->whereNull('completed_at');
                })
                ->whereRelation('group', 'slug', '!=', 'banned')
                ->where('id', '!=', 1) // System
                ->latest()
                ->paginate(25),
        ]);
    }
}
