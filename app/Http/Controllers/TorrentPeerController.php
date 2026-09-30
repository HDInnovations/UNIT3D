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

use App\Models\Peer;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TorrentPeerController extends Controller
{
    /**
     * Live seeder/leecher/completed counts for the torrent page, counted the same way as the page itself.
     */
    public function counts(int $id): JsonResponse
    {
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)
            ->select(['id', 'times_completed'])
            ->withCount([
                'seeds'   => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
                'leeches' => fn ($query) => $query->where('active', '=', true)->where('visible', '=', true),
            ])
            ->findOrFail($id);

        return response()->json([
            'seeders'         => $torrent->seeds_count,
            'leechers'        => $torrent->leeches_count,
            'times_completed' => $torrent->times_completed,
        ]);
    }

    /**
     * Display Peers Of A Torrent.
     */
    public function index(int $id, Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);

        return view('torrent.peers', [
            'torrent' => $torrent,
            'peers'   => Peer::query()
                ->with('user.group')
                ->select(['torrent_id', 'user_id', 'uploaded', 'downloaded', 'left', 'port', 'agent', 'created_at', 'updated_at', 'seeder', 'active', 'visible', 'connectable'])
                ->selectRaw('INET6_NTOA(ip) as ip')
                ->where('torrent_id', '=', $id)
                ->orderByRaw('user_id = ? DESC', [$request->user()->id])
                ->orderByDesc('active')
                ->orderByDesc('seeder')
                ->get()
                ->map(function ($peer) use ($torrent) {
                    $progress = 100 * (1 - $peer->left / $torrent->size);
                    $peer['progress'] = match (true) {
                        0 < $progress && $progress < 1    => 1,
                        99 < $progress && $progress < 100 => 99,
                        default                           => round($progress),
                    };

                    return $peer;
                }),
        ]);
    }
}
