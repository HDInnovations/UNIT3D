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

namespace App\Http\Livewire;

use App\Models\Peer;
use App\Models\User;
use Livewire\Component;

/**
 * Top navigation transfer statistics, polled so they follow announces live.
 *
 * Reads the user and their peers straight from the database: the authenticated
 * user is cached for 30 s and announce batches update transfer totals with
 * mass upserts that do not invalidate that cache.
 */
class RatioBar extends Component
{
    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::query()->findOrFail(auth()->id());

        $peers = Peer::query()
            ->where('user_id', '=', $user->id)
            ->where('active', '=', true)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('COALESCE(SUM(seeder = FALSE), 0) AS leeching')
            ->toBase()
            ->first();

        return view('livewire.ratio-bar', [
            'user'     => $user,
            'seeding'  => (int) $peers->total - (int) $peers->leeching,
            'leeching' => (int) $peers->leeching,
        ]);
    }
}
