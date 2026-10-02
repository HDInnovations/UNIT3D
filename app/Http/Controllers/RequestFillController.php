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

use App\Http\Requests\StoreRequestFillRequest;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Notifications\NewRequestFill;
use App\Notifications\NewRequestFillReject;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RequestControllerTest
 */
class RequestFillController extends Controller
{
    /**
     * Fill A Torrent Request.
     */
    public function store(StoreRequestFillRequest $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        $torrent = Torrent::query()->find(basename((string) $request->torrent_id));

        if ($torrent === null) {
            return to_route('requests.show', ['torrentRequest' => $torrentRequest])
                ->withErrors("Submitted torrent link not found or not yet approved.");
        }

        $torrentRequest->update([
            'filled_by'   => $request->user()->id,
            'torrent_id'  => $torrent->id,
            'filled_when' => now(),
            'filled_anon' => $request->filled_anon,
        ]);

        // Send Private Message
        $sender = $request->boolean('filled_anon') ? 'Anonymous' : $request->user()->username;
        $requester = $torrentRequest->user;

        $requester->notify(new NewRequestFill($torrentRequest));

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.pending-approval'));
    }

    /**
     * Reject A Torrent Request Fill.
     */
    public function destroy(Request $request, TorrentRequest $torrentRequest): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->id === $torrentRequest->user_id || $request->user()->group->is_modo, 403);

        $filler = $torrentRequest->filler;
        $requester = $torrentRequest->user;
        $approver = $request->user();

        $torrentRequest->update([
            'filled_by'   => null,
            'filled_when' => null,
            'torrent_id'  => null,
        ]);

        $filler->notify(new NewRequestFillReject('torrent', $approver->is($requester) ? ($torrentRequest->anon ? 'Anonymous' : $requester->username) : $approver->username, $torrentRequest));

        return to_route('requests.show', ['torrentRequest' => $torrentRequest])
            ->with('success', trans('request.request-reset'));
    }
}
