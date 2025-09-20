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

use App\Enums\ModerationStatus;
use App\Helpers\Bencode;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\TorrentDownload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TorrentDownloadController extends Controller
{
    /**
     * Download Check.
     */
    public function show(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('torrent.download-check', [
            'torrent' => Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id),
            'user'    => $request->user(),
        ]);
    }

    /**
     * Download A Torrent.
     */
    public function store(Request $request, int $id, ?string $rsskey = null): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();

        if (!$user && $rsskey) {
            $user = User::where('rsskey', '=', $rsskey)->sole();
        }
        $torrent = Torrent::withoutGlobalScope(ApprovedScope::class)->findOrFail($id);
        $hasHistory = $user->history()->where([['torrent_id', '=', $torrent->id], ['seeder', '=', 1]])->exists();

        // User's ratio is too low
        if ($user->ratio < config('other.ratio') && !($torrent->user_id === $user->id || $hasHistory)) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('Your ratio is too low to download!');
        }

        // User's download rights are revoked
        if ($user->can_download == 0 && !($torrent->user_id === $user->id || $hasHistory)) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('Your download rights have been revoked!');
        }

        // Torrent Status Is Rejected
        if ($torrent->status === ModerationStatus::REJECTED) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('This torrent has been rejected by staff');
        }

        // The torrent file exist ?
        if (!Storage::disk('torrent-files')->exists($torrent->file_name)) {
            return to_route('torrents.show', ['id' => $torrent->id])
                ->withErrors('Torrent file not found! Please report this torrent!');
        }

        if (!$request->user() && !($rsskey && $user)) {
            return to_route('login');
        }

        $torrentDownload = new TorrentDownload();
        $torrentDownload->user_id = $user->id;
        $torrentDownload->torrent_id = $id;
        $torrentDownload->type = $rsskey ? 'RSS/API using '.$request->header('User-Agent') : 'Site using '.$request->header('User-Agent');
        $torrentDownload->save();

        return response()->streamDownload(
            function () use ($id, $user, $torrent): void {
                $dict = Bencode::bdecode(Storage::disk('torrent-files')->get($torrent->file_name));

                // Set the announce key and add the user passkey
                $dict['announce'] = route('announce', ['passkey' => $user->passkey]);

                // Set link to torrent as the comment
                if (config('torrent.comment')) {
                    $dict['comment'] = config('torrent.comment').'. '.route('torrents.show', ['id' => $id]);
                } else {
                    $dict['comment'] = route('torrents.show', ['id' => $id]);
                }

                echo Bencode::bencode($dict);
            },
            sanitize_filename('['.config('torrent.source').']'.$torrent->name.'.torrent'),
            ['Content-Type' => 'application/x-bittorrent']
        );
    }
}
