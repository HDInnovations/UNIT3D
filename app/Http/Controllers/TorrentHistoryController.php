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

use App\Models\History;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Http\Request;

class TorrentHistoryController extends Controller
{
    /**
     * Display History Of A Torrent.
     */
    public function index(int $id, Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('torrent.history', [
            'torrent'   => Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id),
            'histories' => History::query()
                ->with(['user.group'])
                ->where('torrent_id', '=', $id)
                ->orderByRaw('user_id = ? DESC', [$request->user()->id])
                ->latest()
                ->get(),
        ]);
    }
}
