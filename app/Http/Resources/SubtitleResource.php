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
     * Episode subtitles attached to a season pack or complete series pack have
     * a null episode and their pack type in `pack`. Anonymous uploaders are
     * returned as `Anonymous`, like in the torrent API.
     *
     * `release_match` is only set when the search was given a file size or
     * name, and tells whether the torrent contains a file of that size or name.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        $torrent = $this->torrent;
        $isEpisode = $torrent->season_number !== null;
        $season = $isEpisode ? (int) $torrent->season_number : null;
        $episode = $isEpisode ? (int) $torrent->episode_number : null;
        $pack = match (true) {
            !$isEpisode || $episode !== 0 => null,
            $season === 0                 => 'series',
            default                       => 'season',
        };
        $attributes = $torrent->getAttributes();
        $releaseMatch = \array_key_exists('file_size_match', $attributes) || \array_key_exists('file_name_match', $attributes)
            ? [
                'file_size' => isset($attributes['file_size_match']) ? (bool) $attributes['file_size_match'] : null,
                'file_name' => isset($attributes['file_name_match']) ? (bool) $attributes['file_name_match'] : null,
            ]
            : null;

        return [
            'id'               => $this->id,
            'language'         => $this->language->code,
            'language_name'    => $this->language->name,
            'extension'        => strtolower(ltrim($this->extension, '.')),
            'filename'         => app(SubtitleDownloadService::class)->downloadFilename($this->resource, $torrent),
            'size'             => $this->file_size,
            'downloads'        => $this->downloads ?? 0,
            'uploader'         => $this->anon ? 'Anonymous' : $this->user->username,
            'forced'           => null,
            'hearing_impaired' => null,
            'torrent_id'       => $this->torrent_id,
            'release'          => $torrent->name,
            'type'             => $isEpisode ? 'episode' : 'movie',
            'season'           => $pack === 'series' ? null : $season,
            'episode'          => $pack === null ? $episode : null,
            'pack'             => $pack,
            'tmdb_id'          => ($isEpisode ? $torrent->tmdb_tv_id : $torrent->tmdb_movie_id) ?: null,
            'tvdb_id'          => $isEpisode ? ($torrent->tvdb ?: null) : null,
            'imdb_id'          => $torrent->imdb ? \sprintf('tt%07d', $torrent->imdb) : null,
            'release_match'    => $releaseMatch,
            'created_at'       => $this->created_at?->toIso8601String(),
            'download_url'     => route('api.subtitles.download', ['id' => $this->id], false),
        ];
    }
}
