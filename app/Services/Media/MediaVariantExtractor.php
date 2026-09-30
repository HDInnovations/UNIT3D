<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Helpers\MediaInfo;
use App\Models\MediaEdition;
use App\Models\MediaVariant;
use App\Models\Torrent;

final class MediaVariantExtractor
{
    public function store(Torrent $torrent, string $kind = 'standard', ?string $name = null, ?int $releaseYear = null, ?string $provenance = null): void
    {
        if ($torrent->mediainfo === null || !($torrent->category->movie_meta || $torrent->category->tv_meta)) {
            return;
        }

        $mediaInfo = (new MediaInfo())->parse($torrent->mediainfo);
        $video = $mediaInfo['video'][0] ?? [];
        $audio = $mediaInfo['audio'] ?? [];
        $edition = MediaEdition::query()->firstOrCreate([
            'tmdb_movie_id' => $torrent->tmdb_movie_id,
            'tmdb_tv_id'    => $torrent->tmdb_tv_id,
            'kind'          => $kind,
            'name'          => $name,
        ], ['release_year' => $releaseYear, 'provenance' => $provenance]);
        $variant = MediaVariant::query()->firstOrCreate([
            'media_edition_id' => $edition->id,
            'container'        => $mediaInfo['general']['format'] ?? null,
            'video_codec'      => $video['format'] ?? null,
            'resolution'       => isset($video['width'], $video['height']) ? "{$video['width']} × {$video['height']}" : null,
            'video_bit_rate'   => $video['bit_rate'] ?? null,
            'overall_bit_rate' => $mediaInfo['general']['bit_rate'] ?? null,
        ], [
            'source'             => $torrent->type?->name,
            'hdr'                => $video['hdr_format'] ?? null,
            'duration'           => $mediaInfo['general']['duration'] ?? null,
            'audio_tracks'       => array_map(fn (array $track): array => array_intersect_key($track, array_flip(['language', 'format', 'channels', 'bit_rate', 'title'])), $audio),
            'subtitle_languages' => array_values(array_unique(array_filter(array_column($mediaInfo['text'] ?? [], 'language')))),
        ]);

        $torrent->updateQuietly(['media_variant_id' => $variant->id]);
    }
}
