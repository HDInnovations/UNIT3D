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

use App\Enums\ModerationStatus;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;

class TorrentPendingController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('torrent.pending', [
            'torrents' => Torrent::query()->withoutGlobalScope(ApprovedScope::class)
                ->with(['category', 'type', 'resolution'])
                ->where('status', '=', ModerationStatus::PENDING)
                ->orWhere('status', '=', ModerationStatus::POSTPONED)
                ->get(),
        ]);
    }
}
