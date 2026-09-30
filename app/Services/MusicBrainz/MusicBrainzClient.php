<?php

declare(strict_types=1);

namespace App\Services\MusicBrainz;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MusicBrainzClient
{
    /**
     * @return array<string, mixed>
     */
    public function release(string $mbid): array
    {
        $release = Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->get("https://musicbrainz.org/ws/2/release/{$mbid}", [
                'fmt' => 'json',
                'inc' => 'artist-credits+labels+recordings+release-groups',
            ])
            ->throw()
            ->json();

        if (!\is_array($release) || !isset($release['id'], $release['title'])) {
            throw new RuntimeException("MusicBrainz release {$mbid} was not found.");
        }

        return $release;
    }

    private function userAgent(): string
    {
        return \sprintf('%s metadata lookup (%s)', config('app.name'), config('app.url'));
    }
}
