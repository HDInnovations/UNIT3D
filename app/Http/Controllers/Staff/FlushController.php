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

use App\Events\MessageDeleted;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Message;
use App\Models\Peer;
use App\Repositories\ChatRepository;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\FlushControllerTest
 */
class FlushController extends Controller
{
    /**
     * FlushController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Flush All Old Peers From Database.
     *
     * @throws Exception
     */
    public function peers(): \Illuminate\Http\RedirectResponse
    {
        if (config('announce.external_tracker.is_enabled')) {
            return redirect()->back()->withErrors("The external tracker doesn't support flushing peers.");
        }

        $carbon = new Carbon();
        $peers = Peer::select(['torrent_id', 'user_id', 'peer_id', 'updated_at'])->where('updated_at', '<', $carbon->copy()->subHours(2))->get();

        foreach ($peers as $peer) {
            History::query()
                ->where('torrent_id', '=', $peer->torrent_id)
                ->where('user_id', '=', $peer->user_id)
                ->update([
                    'active'     => false,
                    'updated_at' => DB::raw('updated_at'),
                ]);

            Peer::query()
                ->where('torrent_id', '=', $peer->torrent_id)
                ->where('user_id', '=', $peer->user_id)
                ->where('peer_id', '=', $peer->peer_id)
                ->delete();
        }

        return to_route('staff.dashboard.index')
            ->with('success', 'Ghost peers have been flushed');
    }

    /**
     * Flush All Chat Messages.
     *
     * @throws Exception
     */
    public function chat(): \Illuminate\Http\RedirectResponse
    {
        foreach (Message::all() as $message) {
            broadcast(new MessageDeleted($message));
            $message->delete();
        }

        $this->chatRepository->systemMessage(
            'Chatbox Has Been Flushed! :broom:'
        );

        return to_route('staff.dashboard.index')
            ->with('success', 'Chatbox has been flushed');
    }
}
