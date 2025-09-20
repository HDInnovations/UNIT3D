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
use App\Models\Audit;
use App\Models\User;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\AuditControllerTest
 */
class AuditController extends Controller
{
    /**
     * Display All Audits.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.audit.index', [
            'staffUsers' => User::query()
                ->with(['group'])
                ->whereHas('group', function ($query): void {
                    $query->where('is_modo', '=', true)
                        ->orWhere('is_editor', '=', true);
                })
                ->withCount([
                    'audits as total_actions' => fn ($query) => $query->where('action', '!=', 'create'),
                    'audits as last_60_days'  => fn ($query) => $query->where('action', '!=', 'create')->whereBetween('created_at', [now()->subDays(60), now()]),
                    'audits as last_30_days'  => fn ($query) => $query->where('action', '!=', 'create')->whereBetween('created_at', [now()->subDays(30), now()]),
                ])
                ->get()
                ->sortBy(fn ($user) => $user->group->level)
        ]);
    }

    /**
     * Delete A Audit.
     *
     * @throws Exception
     */
    public function destroy(Audit $audit): \Illuminate\Http\RedirectResponse
    {
        $audit->delete();

        return to_route('staff.audits.index')
            ->with('success', 'Audit record has successfully been deleted');
    }
}
