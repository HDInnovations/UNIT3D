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

namespace App\Http\Controllers;

use App\Http\Requests\Staff\StoreReportAssigneeRequest;
use App\Models\Report;
use App\Models\User;
use App\Notifications\NewReportAssigned;
use Illuminate\Http\Request;

class ReportAssigneeController extends Controller
{
    final public function store(StoreReportAssigneeRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $report->update($request->validated());

        $assignedStaff = User::query()->findOrFail($request->integer('staff_id'));

        $assignedStaff->notify(new NewReportAssigned($report));

        return to_route('reports.show', ['report' => $report])
            ->with('success', trans('ticket.assigned-success'));
    }

    final public function destroy(Request $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $report->update([
            'staff_id' => null,
        ]);

        return to_route('reports.show', ['report' => $report])
            ->with('success', trans('ticket.unassigned-success'));
    }
}
