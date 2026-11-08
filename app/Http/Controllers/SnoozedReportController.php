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

namespace App\Http\Controllers;

use App\Http\Requests\Staff\StoreSnoozedReportRequest;
use App\Models\Report;
use Illuminate\Http\Request;

class SnoozedReportController extends Controller
{
    /**
     * Snooze A Report.
     */
    public function store(StoreSnoozedReportRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $report->update($request->validated());

        return to_route('reports.show', ['report' => $report])
            ->with('success', 'Report has been snoozed');
    }

    /**
     * Un-snooze A Report.
     */
    public function destroy(Request $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo, 403);

        $report->update(['snoozed_until' => null]);

        return to_route('reports.show', ['report' => $report])
            ->with('success', 'Report has been un-snoozed');
    }
}
