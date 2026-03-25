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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace Database\Seeders;

use App\Models\TorrentDeletionReason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TorrentDeletionReasonSeeder extends Seeder
{
    final public function run(): void
    {
        TorrentDeletionReason::query()->upsert([
            [
                'name'        => 'Bad Aspect Ratio',
                'description' => 'An encoding error is causing this upload to be displayed at an improper aspect ratio',
            ],
            [
                'name'        => 'Non-OAR',
                'description' => 'This upload has an aspect ratio different than the original, theatrically presented movie. Once a release with proper aspect ratio is available, no new non-OAR upload may be uploaded in the same resolution group.',
            ],
            [
                'name'        => 'Bloated',
                'description' => 'The video or audio bitrate for this upload is too high.'
            ],
            [
                'name'        => 'Redundant Audio Track(s)',
                'description' => 'This upload includes superfluous audio tracks such as non-English dubs, or redundant versions of the same track.'
            ],
            [
                'name'        => 'Deinterlacing Issues',
                'description' => 'This upload has been improperly deinterlaced.'
            ],
            [
                'name'        => 'Improper Framerate',
                'description' => 'This upload plays at a framerate different than the native, proper framerate for this title.'
            ],
            [
                'name'        => 'Improperly Synchronized Subtitles',
                'description' => 'Subtitles included with this upload are usable, but not properly synchronized.'
            ],
            [
                'name'        => 'Improper Codec/Container',
                'description' => 'This upload does not conform to our preferred formats.'
            ],
            [
                'name'        => 'Non-Conform Resolution',
                'description' => 'This upload does not conform to our preferred resolutions.'
            ],
            [
                'name'        => 'Inferior Source',
                'description' => 'This source does not provide the best viewing experience currently available.'
            ],
            [
                'name'        => 'Low Quality',
                'description' => 'This upload was encoded from a particularly poor source, or suffers from major quality issues.'
            ],
            [
                'name'        => 'Playback Issues',
                'description' => 'Issues usually detailed by a second tag are preventing this upload from being perfectly played back or encoded from.'
            ],
            [
                'name'        => 'Incomplete',
                'description' => 'This upload is lacking content.'
            ],
            [
                'name'        => 'No English Subtitles',
                'description' => 'This upload of a non-English movie does not include English subtitles (internally or externally via the Subtitle Manager).',
            ],
            [
                'name'        => 'No Forced English Subtitles',
                'description' => 'This upload does not include separate English subtitles for significant non-English dialogue.'
            ],
            [
                'name'        => 'Non-English Language Dub',
                'description' => 'This upload includes neither the original audio nor an English dub, only a non-English dub.'
            ],
            [
                'name'        => 'Out of Sync. Audio',
                'description' => 'Audio included with this upload is usable, but not properly synchronized.'
            ],
            [
                'name'        => 'Poor Cropping',
                'description' => 'This upload was significantly overcropped or undercropped.'
            ],
            [
                'name'        => 'Poorly Translated Subtitles',
                'description' => 'Subtitles included with this upload are poor quality and not an accurate translation of the movie.'
            ],
            [
                'name'        => 'Hardcoded Subtitles',
                'description' => 'Subtitles have been hardcoded in the video track of this upload. Hardcoded forced subtitles are not targeted by this tag.'
            ],
            [
                'name'        => 'Transcoded Audio',
                'description' => 'The audio track included with this upload was transcoded from an already compressed, lossy source.'
            ],
            [
                'name'        => 'Watermarked',
                'description' => 'This upload is watermarked in a significant way.'
            ],
            [
                'name'        => 'Upscale',
                'description' => 'This upload was encoded from a low resolution source.'
            ],
            [
                'name'        => 'Custom Disc',
                'description' => 'This upload is not a 1:1 rip of a retail disc.'
            ],
            [
                'name'        => 'Foreign Overlays',
                'description' => 'The video has text overlays or intertitles in a language other than the movie\'s original language or English.'
            ],
        ], ['id'], ['updated_at' => DB::raw('updated_at')]);
    }
}
