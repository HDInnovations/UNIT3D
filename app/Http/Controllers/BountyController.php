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
use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Notifications\NewRequestBounty;
use App\Repositories\ChatRepository;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RequestControllerTest
 */
class BountyController extends Controller
{
    /**
     * RequestController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Add Bounty To A Torrent Request.
     */
    public function store(StoreTorrentRequestBountyRequest $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        abort_unless($torrentRequest->approved_when === null, 403);

        $user = $request->user();

        $user->decrement('seedbonus', $request->integer('seedbonus'));

        $bounty = $torrentRequest->bounties()->create(['user_id' => $user->id] + $request->validated());

        $torrentRequest->increment('bounty', $request->integer('seedbonus'));

        $torrentRequest->created_at = now();
        $torrentRequest->save();

        if ($request->boolean('anon') == 0) {
            $this->chatRepository->systemMessage(
                trans('application-messages.bot.bounty-added', [
                    'userUrl' => href_profile($user),
                    'username' => $user->username,
                    'bon' => $request->input('seedbonus'),
                    'requestUrl' => href_request($torrentRequest),
                    'name' => $torrentRequest->name,
                ], config('app.locale'))
            );
        } else {
            $this->chatRepository->systemMessage(
                trans('application-messages.bot.bounty-added-anon', [
                    'bon' => $request->input('seedbonus'),
                    'requestUrl' => href_request($torrentRequest),
                    'name' => $torrentRequest->name,
                ], config('app.locale'))
            );
        }

        $requester = $torrentRequest->user;

        $requester->notify(new NewRequestBounty($bounty));

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.added-bonus'));
    }

    public function update(UpdateTorrentRequestBountyRequest $request, TorrentRequest $torrentRequest, TorrentRequestBounty $torrentRequestBounty): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->group->is_modo || $request->user()->id === $torrentRequestBounty->user_id, 403);

        $torrentRequestBounty->update($request->validated());

        return back();
    }
}
