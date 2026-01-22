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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreReportAssigneeRequest;
use App\Models\Report;
use App\Models\User;
use App\Notifications\NewReportAssigned;
use Illuminate\Http\Request;

class ReportAssigneeController extends Controller
{
    final public function store(StoreReportAssigneeRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->update($request->validated());

        $assignedStaff = User::query()->findOrFail($request->integer('assigned_to'));

        $assignedStaff->notify(new NewReportAssigned($report));

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', trans('ticket.assigned-success'));
    }

    final public function destroy(Request $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->update([
            'assigned_to' => null,
        ]);

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', trans('ticket.unassigned-success'));
    }
}
