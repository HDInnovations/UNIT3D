<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\TorrentMetadata;
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
        $release = $musicBrainz->release($this->mbid);
        $artists = collect($release['artist-credit'] ?? [])
            ->pluck('name')
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');

        TorrentMetadata::query()->updateOrCreate(['torrent_id' => $this->torrentId], [
            'source'      => 'musicbrainz',
            'source_id'   => $release['id'],
            'title'       => $release['title'],
            'subtitle'    => $artists === '' ? null : $artists,
            'released_on' => $release['date'] ?? null,
            'publisher'   => $release['label-info'][0]['label']['name'] ?? null,
            'item_count'  => \count($release['media'][0]['tracks'] ?? []),
            'summary'     => null,
            'source_url'  => "https://musicbrainz.org/release/{$release['id']}",
            'raw'         => $release,
        ]);
    }
}
