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

use App\Enums\TorrentReseedRequestResult;
use App\Models\Torrent;
use App\Services\TorrentReseedService;
use Illuminate\Http\Request;

class TorrentReseedController extends Controller
{
    /**
     * TorrentReseedController Constructor.
     */
    public function __construct(private readonly TorrentReseedService $torrentReseedService)
    {
    }

    /**
     * Display a listing of torrent reseed requests.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('torrent-reseed.index');
    }

    /**
     * Reseed Request A Torrent.
     */
    public function store(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $torrent = Torrent::query()->findOrFail($id);
        $result = $this->torrentReseedService->request($torrent, $request->user());

        return match ($result) {
            TorrentReseedRequestResult::AlreadyRequested => to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('You have already made a reseed request for this torrent.'),
            TorrentReseedRequestResult::Counted => to_route('torrents.show', ['id' => $torrent->id])
                ->with('success', 'A reseed request already exists. Your request has been counted.'),
            TorrentReseedRequestResult::Created => to_route('torrents.show', ['id' => $torrent->id])
                ->with('success', 'A notification has been sent to all users that downloaded this torrent along with original uploader!'),
            TorrentReseedRequestResult::Ineligible => to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('This torrent doesn\'t meet the rules for a reseed request.'),
        };
    }
}
