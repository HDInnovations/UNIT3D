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

namespace App\Http\Resources;

use App\Services\SubtitleDownloadService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Override;

/**
 * @mixin \App\Models\Subtitle
 */
class SubtitleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * UNIT3D does not record whether a subtitle is forced or for the hearing
     * impaired, so those flags are always null (unknown).
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'language'         => $this->language->code,
            'language_name'    => $this->language->name,
            'extension'        => strtolower(ltrim($this->extension, '.')),
            'filename'         => app(SubtitleDownloadService::class)->downloadFilename($this->resource, $this->torrent),
            'size'             => $this->file_size,
            'downloads'        => $this->downloads ?? 0,
            'forced'           => null,
            'hearing_impaired' => null,
            'torrent_id'       => $this->torrent_id,
            'release'          => $this->torrent->name,
            'tmdb_id'          => $this->torrent->tmdb_movie_id ?: null,
            'imdb_id'          => $this->torrent->imdb ? \sprintf('tt%07d', $this->torrent->imdb) : null,
            'created_at'       => $this->created_at?->toIso8601String(),
            'download_url'     => route('api.subtitles.download', ['id' => $this->id], false),
        ];
    }
}
