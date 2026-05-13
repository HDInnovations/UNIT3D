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
use App\Http\Requests\Staff\StoreReportNoteRequest;
use App\Models\Report;
use App\Models\ReportNote;

class ReportNoteController extends Controller
{
    /**
     * Store a newly created report staff note.
     */
    public function store(StoreReportNoteRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $report->notes()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', 'Report note has been successfully created.');
    }

    /**
     * Remove the specified report staff note.
     */
    public function destroy(Report $report, ReportNote $reportNote): \Illuminate\Http\RedirectResponse
    {
        abort_unless($reportNote->report_id === $report->id, 404);

        $reportNote->delete();

        return to_route('staff.reports.show', ['report' => $report])
            ->with('success', 'Report note has been successfully deleted.');
    }
}
