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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreSnoozedReportRequest;
use App\Models\Report;

class SnoozedReportController extends Controller
{
    /**
     * Snooze A Report.
     */
    public function store(StoreSnoozedReportRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->update($request->validated());

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', __('application-messages.flash.report-snoozed'));
    }

    /**
     * Un-snooze A Report.
     */
    public function destroy(Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->update(['snoozed_until' => null]);

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', __('application-messages.flash.report-unsnoozed'));
    }
}
