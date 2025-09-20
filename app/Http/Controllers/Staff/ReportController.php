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
use App\Http\Requests\Staff\UpdateReportRequest;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\Report;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\ReportControllerTest
 */
class ReportController extends Controller
{
    /**
     * Display All Reports.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.report.index');
    }

    /**
     * Show A Report.
     */
    public function show(Report $report): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        // cspell:ignore punct
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', (string) $report->message, $match);

        return view('Staff.report.show', ['report' => $report, 'urls' => $match[0]]);
    }

    /**
     * Update A Report.
     */
    public function update(UpdateReportRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $staff = auth()->user();

        if ($report->solved) {
            return to_route('staff.reports.index')
                ->withErrors('This report has already been solved');
        }

        $report->update(['solved' => true, 'staff_id' => $staff->id] + $request->validated());

        $conversation = Conversation::create(['subject' => 'Your Report Has A New Verdict']);

        $conversation->users()->sync([$staff->id => ['read' => true], $report->reporter_id]);

        PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $staff->id,
            'message'         => '[b]REPORT TITLE:[/b] '.$report->title."\n\n[b]ORIGINAL MESSAGE:[/b] ".$report->message."\n\n[b]VERDICT:[/b] ".$report->verdict,
        ]);

        return to_route('staff.reports.index')
            ->with('success', 'Report has been successfully resolved');
    }
}
