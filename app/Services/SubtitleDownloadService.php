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

namespace App\Services;

use App\Models\Subtitle;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared subtitle download logic used by both the web UI and the API.
 */
class SubtitleDownloadService
{
    /**
     * Determine if the user's download rights allow downloading the subtitle.
     */
    public function userCanDownload(User $user, Subtitle $subtitle): bool
    {
        return $user->can_download || $subtitle->user_id == $user->id;
    }

    /**
     * Get the user-facing filename of the subtitle download.
     */
    public function downloadFilename(Subtitle $subtitle, Torrent $torrent): string
    {
        return sanitize_filename('['.$subtitle->language->name.' Subtitle]'.$torrent->name.$subtitle->extension);
    }

    /**
     * Stream the subtitle file from storage and record the download.
     *
     * Aborts with a 404 when the subtitle's torrent is not visible (e.g. not
     * approved) or when the file is missing from storage.
     */
    public function download(Subtitle $subtitle): StreamedResponse
    {
        $torrent = $subtitle->torrent;

        abort_if($torrent === null, 404);

        $disk = Storage::disk('subtitle-files');

        abort_unless($disk->exists($subtitle->file_name), 404, 'Subtitle file not found.');

        // Increment downloads count
        $subtitle->increment('downloads');

        return $disk->download($subtitle->file_name, $this->downloadFilename($subtitle, $torrent), [
            'Content-Type' => $disk->mimeType($subtitle->file_name) ?: 'application/octet-stream',
        ]);
    }
}
