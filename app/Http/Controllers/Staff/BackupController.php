<?php
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

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\BackupControllerTest
 */
class BackupController extends Controller
{
    /**
     * Display All Backups.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        \abort_unless($request->user()->hasRole('coder'), 403);

        return \view('Staff.backup.index');
    }
}
