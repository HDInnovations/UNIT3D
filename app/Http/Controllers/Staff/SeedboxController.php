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
use App\Models\Seedbox;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\SeedboxControllerTest
 */
class SeedboxController extends Controller
{
    /**
     * Display All Registered Seedboxes.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.seedbox.index', [
            'seedboxes' => Seedbox::with('user.group')->latest()->paginate(50),
        ]);
    }

    /**
     * Delete A Registered Seedbox.
     *
     * @throws Exception
     */
    public function destroy(Seedbox $seedbox): \Illuminate\Http\RedirectResponse
    {
        $seedbox->delete();

        return to_route('staff.seedboxes.index')
            ->with('success', 'Seedbox record has successfully been deleted');
    }
}
