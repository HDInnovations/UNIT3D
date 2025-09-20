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

use App\Http\Requests\Staff\StoreTorrentTrumpRequest;
use App\Models\Torrent;
use Illuminate\Http\Request;

class TorrentTrumpController extends Controller
{
    public function store(StoreTorrentTrumpRequest $request, Torrent $torrent): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo, 403);

        $torrent->trump()->create([
            'user_id' => $user->id,
            'reason'  => $request->input('reason'),
        ]);

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent flagged as trumpable!');
    }

    public function destroy(Request $request, Torrent $torrent): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->group->is_modo, 403);

        $torrent->trump()->delete();

        return to_route('torrents.show', ['id' => $torrent->id])
            ->with('success', 'Torrent trump flag removed!');
    }
}
