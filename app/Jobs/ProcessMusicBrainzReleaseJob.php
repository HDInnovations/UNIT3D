<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use App\Services\Media\MediaWorkCatalog;
use App\Services\MusicBrainz\MusicBrainzClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessMusicBrainzReleaseJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $torrentId, public string $mbid)
    {
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping("musicbrainz-release:{$this->mbid}")->dontRelease()->expireAfter(30),
            new RateLimited(GlobalRateLimit::MUSICBRAINZ),
        ];
    }

    public function handle(MusicBrainzClient $musicBrainz): void
    {
        $metadata = $musicBrainz->lookup($this->mbid);
        $release = $metadata['data'];
        $entity = $metadata['entity'];
        $artists = collect($release['artist-credit'] ?? [])
            ->pluck('name')
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');

        $itemCount = $entity === 'release'
            ? collect($release['media'] ?? [])
                ->sum(fn (mixed $medium): int => \is_array($medium) ? \count($medium['tracks'] ?? []) : 0)
            : null;

        $publishers = collect($release['label-info'] ?? [])
            ->map(fn (mixed $labelInfo): ?string => \is_array($labelInfo) && \is_string($labelInfo['label']['name'] ?? null) ? $labelInfo['label']['name'] : null)
            ->filter()
            ->unique()
            ->join(', ');

        $catalog = app(MediaWorkCatalog::class);

        TorrentMetadata::query()->updateOrCreate(['torrent_id' => $this->torrentId], [
            'source'      => 'musicbrainz',
            'source_id'   => $release['id'],
            'title'       => $release['title'],
            'subtitle'    => $artists === '' ? null : $artists,
            'released_on' => $release['date'] ?? $release['first-release-date'] ?? null,
            'publisher'   => $publishers === '' ? null : $publishers,
            'item_count'  => $itemCount,
            'summary'     => null,
            'source_url'  => "https://musicbrainz.org/{$entity}/{$release['id']}",
            'raw'         => $release,
            // This release's own facets (format in particular legitimately
            // differs between editions of the same album, e.g. CD vs
            // Vinyl); category filters match on this, not the Work's shared
            // last-synced snapshot, so every accessible edition stays findable.
            'facets' => $catalog->computeFacets('music', $release),
        ]);

        $torrent = Torrent::query()
            ->withoutGlobalScope(ApprovedScope::class)
            ->with(['category', 'metadata'])
            ->find($this->torrentId);

        if ($torrent !== null) {
            $catalog->sync($torrent);

            Torrent::query()->whereKey($torrent->id)->searchable();
        }
    }
}
