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

use App\Http\Requests\StoreTorrentRequestBountyRequest;
use App\Http\Requests\UpdateTorrentRequestBountyRequest;
use App\Models\Chatroom;
use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\User;
use App\Notifications\Chatbox\BountyCreate;
use App\Notifications\NewRequestBounty;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RequestControllerTest
 */
class BountyController extends Controller
{
    /**
     * Add Bounty To A Torrent Request.
     */
    public function store(StoreTorrentRequestBountyRequest $request, TorrentRequest $torrentRequest, #[CurrentUser] User $currentUser): \Illuminate\Http\RedirectResponse
    {
        abort_unless($torrentRequest->approved_when === null, 403);

        $currentUser->decrement('seedbonus', $request->seedbonus);

        $bounty = $torrentRequest->bounties()->create($request->toArray());

        $torrentRequest->increment('bounty', $request->seedbonus);

        $torrentRequest->bumped_at = now();
        $torrentRequest->save();

        Chatroom::systemChatroom()->notify(new BountyCreate($bounty));

        $requester = $torrentRequest->user;

        $requester->notify(new NewRequestBounty($bounty));

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.added-bonus'));
    }

    public function update(
        UpdateTorrentRequestBountyRequest $request,
        TorrentRequest $torrentRequest,
        TorrentRequestBounty $torrentRequestBounty,
        #[CurrentUser] User $currentUser,
    ): \Illuminate\Http\RedirectResponse {
        abort_unless($currentUser->group->is_modo || $currentUser->id === $torrentRequestBounty->user_id, 403);

        $torrentRequestBounty->update($request->toArray());

        return back();
    }
}
