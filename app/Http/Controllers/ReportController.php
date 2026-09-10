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

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\ReportControllerTest
 */
class ReportController extends Controller
{
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
}
