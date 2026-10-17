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

use App\Helpers\Bencode;
use App\Models\Playlist;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipStream\ZipStream;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PlaylistControllerTest
 */
class PlaylistZipController extends Controller
{
    /**
     * Download All Playlist Torrents.
     */
    public function show(Playlist $playlist): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        //  Extend The Maximum Execution Time
        set_time_limit(300);

        // Authorized User
        $user = auth()->user();

        // Zip File Name
        $zipFileName = '['.$user->username.']'.$playlist->name.'.zip';

        return response()->streamDownload(
            function () use ($zipFileName, $user, $playlist): void {
                $zip = new ZipStream(outputName: sanitize_filename($zipFileName));

                $announceUrl = route('announce', ['passkey' => $user->passkey]);

                $playlist->torrents()->each(function ($torrent) use ($announceUrl, $zip): void {
                    if (Storage::disk('torrent-files')->exists($torrent->file_name)) {
                        $dict = Bencode::bdecode(Storage::disk('torrent-files')->get($torrent->file_name));

                        // Set the announce key and add the user passkey
                        $dict['announce'] = $announceUrl;

                        // Set link to torrent as the comment
                        if (config('torrent.comment')) {
                            $dict['comment'] = config('torrent.comment').'. '.route('torrents.show', ['id' => $torrent->id]);
                        } else {
                            $dict['comment'] = route('torrents.show', ['id' => $torrent->id]);
                        }

                        $fileToDownload = Bencode::bencode($dict);

                        $filename = sanitize_filename('['.config('torrent.source').']'.$torrent->name.'.torrent');

                        $zip->addFile($filename, $fileToDownload);
                    }
                }, 100);

                $zip->finish();
            },
            sanitize_filename($zipFileName),
        );
    }
}
