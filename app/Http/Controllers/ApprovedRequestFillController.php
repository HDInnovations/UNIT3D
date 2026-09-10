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

use App\Achievements\UserFilled100Requests;
use App\Achievements\UserFilled25Requests;
use App\Achievements\UserFilled50Requests;
use App\Achievements\UserFilled75Requests;
use App\Models\TorrentRequest;
use App\Notifications\NewRequestFillApprove;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RequestControllerTest
 */
class ApprovedRequestFillController extends Controller
{
    /**
     * RequestController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Approve A Torrent Request.
     */
    public function store(Request $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        abort_unless(($request->user()->id === $torrentRequest->user_id || $request->user()->group->is_modo) && $torrentRequest->approved_when === null, 403);

        $approver = $request->user();
        $filler = $torrentRequest->filler()->sole();

        $torrentRequest->update([
            'approved_by'   => $approver->id,
            'approved_when' => now(),
        ]);

        $filler->increment('seedbonus', (float) $torrentRequest->bounty);

        // Achievements
        if (!$torrentRequest->filled_anon) {
            $filler->addProgress(new UserFilled25Requests(), 1);
            $filler->addProgress(new UserFilled50Requests(), 1);
            $filler->addProgress(new UserFilled75Requests(), 1);
            $filler->addProgress(new UserFilled100Requests(), 1);
        }

        // Auto Shout
        if ($torrentRequest->filled_anon) {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has filled request, [url=%s]%s[/url]', href_request($torrentRequest), $torrentRequest->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has filled request, [url=%s]%s[/url]', href_profile($filler), $filler->username, href_request($torrentRequest), $torrentRequest->name)
            );
        }

        $filler->notify(new NewRequestFillApprove($torrentRequest));

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', \sprintf(trans('request.approved-user'), $torrentRequest->name, $torrentRequest->filled_anon ? 'Anonymous' : $filler->username));
    }

    /**
     * Destroy A Torrent Request Fill.
     */
    public function destroy(Request $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        abort_unless((bool) $request->user()->group->is_modo, 403);

        $filler = $torrentRequest->filler;

        $torrentRequest->update([
            'approved_by'   => null,
            'approved_when' => null,
        ]);

        // TODO: Change database column to signed from unsigned to handle negative bon.
        $refunded = min($torrentRequest->bounty, (float) $filler->seedbonus);

        $filler->decrement('seedbonus', (float) $refunded);

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.request-reset'));
    }
}
