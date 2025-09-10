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
use App\Models\Peer;
use App\Models\Torrent;
use App\Models\TorrentReseed;
use App\Models\User;
use App\Notifications\NewReseedRequest;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorrentReseedController extends Controller
{
    /**
     * TorrentReseedController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
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
        $torrent = Torrent::findOrFail($id);
        $userId = $request->user()->id;

        $isLeeching = Peer::query()
            ->where('user_id', '=', $userId)
            ->where('torrent_id', '=', $torrent->id)
            ->where('seeder', '=', false)
            ->where('active', '=', true)
            ->where('visible', '=', true)
            ->exists();

        if ($isLeeching && $torrent->seeders <= 2) {
            // Check if this user has already made a reseed request for this torrent
            $existingReseed = TorrentReseed::where('torrent_id', '=', $torrent->id)
                ->where('user_id', '=', $userId)
                ->where('created_at', '>', now()->subDays(30))
                ->exists();

            TorrentReseed::query()->upsert([[
                'torrent_id'     => $torrent->id,
                'user_id'        => $userId,
                'requests_count' => 1,
            ]], ['torrent_id', 'user_id'], [
                'requests_count' => DB::raw('requests_count + 1'),
            ]);

            if ($existingReseed) {
                return to_route('torrents.show', ['id' => $torrent->id])
                    ->withErrors('You have already made a reseed request for this torrent.');
            }

            // Send notifications
            $potentialReseeds = History::where('torrent_id', '=', $torrent->id)->where('active', '=', 0)->get();

            foreach ($potentialReseeds as $potentialReseed) {
                User::find($potentialReseed->user_id)->notify(new NewReseedRequest($torrent));
            }

            $torrentUrl = href_torrent($torrent);

            $this->chatRepository->systemMessage(
                \sprintf('Ladies and Gents, a reseed request was just placed on [url=%s]%s[/url] can you help out?', $torrentUrl, $torrent->name)
            );

            return to_route('torrents.show', ['id' => $torrent->id])
                ->with('success', 'A notification has been sent to all users that downloaded this torrent along with original uploader!');
        }

        return to_route('torrents.show', ['id' => $torrent->id])
            ->withErrors('This torrent doesn\'t meet the rules for a reseed request.');
    }
}
