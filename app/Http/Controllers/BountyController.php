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
use App\Models\User;
use App\Notifications\NewRequestBounty;
use App\Repositories\ChatRepository;
use Illuminate\Container\Attributes\CurrentUser;

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
    public function store(StoreTorrentRequestBountyRequest $request, TorrentRequest $torrentRequest, #[CurrentUser] User $currentUser): \Illuminate\Http\RedirectResponse
    {
        abort_unless($torrentRequest->approved_when === null, 403);

        $currentUser->decrement('seedbonus', $request->seedbonus);

        $bounty = $torrentRequest->bounties()->create($request->toArray());

        $torrentRequest->increment('bounty', $request->seedbonus);

        $torrentRequest->bumped_at = now();
        $torrentRequest->save();

        if (!$request->anon) {
            $this->chatRepository->systemMessage(
                \sprintf(
                    '[url=%s]%s[/url] has added %s BON bounty to request [url=%s]%s[/url]',
                    href_profile($currentUser),
                    $currentUser->username,
                    $request->seedbonus,
                    href_request($torrentRequest),
                    $torrentRequest->name
                )
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf(
                    'An anonymous user added %s BON bounty to request [url=%s]%s[/url]',
                    $request->seedbonus,
                    href_request($torrentRequest),
                    $torrentRequest->name
                )
            );
        }

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
