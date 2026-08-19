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

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportReplyRequest;
use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\ReportControllerTest
 */
class ReportController extends Controller
{
    /**
     * Show all reports.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('report.index');
    }

    /**
     * Create A Report.
     */
    public function store(StoreReportRequest $request): \Illuminate\Http\RedirectResponse
    {
        switch (true) {
            case $request->reported_request_id !== null:
                $torrentRequest = TorrentRequest::query()->whereKey($request->reported_request_id)->sole();

                Report::query()->create([
                    'type'                => 'Request',
                    'reported_request_id' => $torrentRequest->id,
                    'reporter_id'         => $request->user()->id,
                    'reported_user_id'    => $torrentRequest->user_id,
                    'title'               => $torrentRequest->name,
                    'message'             => $request->string('message'),
                ]);

                break;
            case $request->reported_torrent_id !== null:
                $torrent = Torrent::query()->whereKey($request->reported_torrent_id)->sole();

                Report::query()->create([
                    'type'                => 'Torrent',
                    'reported_torrent_id' => $torrent->id,
                    'reporter_id'         => $request->user()->id,
                    'reported_user_id'    => $torrent->user_id,
                    'title'               => $torrent->name,
                    'message'             => $request->string('message'),
                ]);

                break;
            case $request->reported_user_username !== null:
                $user = User::query()->where('username', '=', $request->reported_user_username)->sole();

                Report::query()->create([
                    'type'             => 'User',
                    'reporter_id'      => $request->user()->id,
                    'reported_user_id' => $user->id,
                    'title'            => $user->username,
                    'message'          => $request->string('message'),
                ]);

                break;
        }

        return back()->with('success', __('user.report-sent'));
    }

    /**
     * Show A Report.
     */
    public function show(Request $request, Report $report): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $report->reporter_id, 403);

        if ($request->user()->id === $report->reporter_id) {
            $report->user_read = true;
        }

        if ($request->user()->id === $report->staff_id) {
            $report->staff_read = true;
        }

        $report->save();

        return view('report.show', [
            'report' => $report->load('replies', 'staff'),
            'user'   => $request->user(),
            'staff'  => User::query()
                ->whereRelation('group', 'is_modo', '=', true)
                ->whereRelation('group', 'slug', '!=', 'bot')
                ->get(),
        ]);
    }

    /**
     * Resolve a report.
     */
    public function update(Request $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $report->reporter_id, 403);

        if ($report->solved_at !== null) {
            return to_route('reports.index')
                ->withErrors('This report has already been resolved');
        }

        $report->update([
            'solved_at' => now(),
        ]);

        return to_route('reports.index')
            ->with('success', 'Report has been successfully resolved');
    }

    /**
     * Update A Report.
     */
    public function reply(StoreReportReplyRequest $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $report->reporter_id, 403);

        $report->update([
            'staff_read' => false,
            'user_read'  => false,
        ]);

        ReportReply::query()->create([
            ...$request->validated(),
            'report_id' => $report->id,
            'user_id'   => $request->user()->id,
        ]);

        return to_route('reports.show', ['report' => $report]);
    }
}
