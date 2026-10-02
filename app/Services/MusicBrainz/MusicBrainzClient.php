<?php

declare(strict_types=1);

namespace App\Services\MusicBrainz;

use App\Services\Metadata\ProviderQueryEscaper;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MusicBrainzClient
{
    /**
     * Bounded release-group search by free text (artist and/or album). The
     * query is escaped for MusicBrainz's Lucene-based search syntax so
     * user input can never inject field selectors or boolean operators.
     *
     * @return list<array<string, mixed>>
     */
    public function searchReleaseGroups(string $query, int $limit = 12): array
    {
        $limit = max(1, min($limit, 12));

        $data = Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->connectTimeout(3)
            ->timeout(8)
            ->get('https://musicbrainz.org/ws/2/release-group/', [
                'query' => ProviderQueryEscaper::lucene($query),
                'fmt'   => 'json',
                'limit' => $limit,
            ])
            ->throw()
            ->json();

        if (!\is_array($data) || !isset($data['release-groups']) || !\is_array($data['release-groups'])) {
            throw new RuntimeException('MusicBrainz release-group search returned an unexpected response.');
        }

        return $data['release-groups'];
    }

    /**
     * Bounded, paginated list of releases (editions) belonging to exactly
     * one, explicitly chosen release-group.
     *
     * @return array{releases: list<array<string, mixed>>, 'release-count': int}
     */
    public function browseReleases(string $releaseGroupId, int $offset, int $limit = 12): array
    {
        $limit = max(1, min($limit, 12));

        $data = Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->connectTimeout(3)
            ->timeout(8)
            ->get('https://musicbrainz.org/ws/2/release/', [
                'release-group' => $releaseGroupId,
                'fmt'           => 'json',
                'inc'           => 'artist-credits+media',
                'limit'         => $limit,
                'offset'        => max(0, $offset),
            ])
            ->throw()
            ->json();

        if (!\is_array($data) || !isset($data['releases']) || !\is_array($data['releases'])) {
            throw new RuntimeException('MusicBrainz release browse returned an unexpected response.');
        }

        return [
            'releases'       => $data['releases'],
            'release-count'  => \is_int($data['release-count'] ?? null) ? $data['release-count'] : \count($data['releases']),
        ];
    }

    /**
     * @return array{entity: 'release'|'release-group', data: array<string, mixed>}
     */
    public function lookup(string $mbid): array
    {
        $entity = 'release';
        $response = $this->request($mbid, $entity);

        // UUIDs do not encode their entity type. Only a missing release permits
        // checking the album itself; an outage must remain a provider error.
        if ($response->notFound()) {
            $entity = 'release-group';
            $response = $this->request($mbid, $entity);
        }

        $data = $response->throw()->json();

        if (!\is_array($data) || !isset($data['id'], $data['title'])) {
            throw new RuntimeException("MusicBrainz metadata {$mbid} was not found.");
        }

        return ['entity' => $entity, 'data' => $data];
    }

    private function request(string $mbid, string $entity): Response
    {
        return Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->get("https://musicbrainz.org/ws/2/{$entity}/{$mbid}", [
                'fmt' => 'json',
                'inc' => $entity === 'release'
                    ? 'artist-credits+labels+recordings+release-groups+media+discids+isrcs'
                        .'+aliases+annotation+tags+genres'
                        .'+artist-rels+label-rels+recording-rels+release-rels+release-group-rels+url-rels+work-rels'
                    : 'artist-credits+aliases+annotation+tags+genres+artist-rels+url-rels',
            ]);
    }

    private function userAgent(): string
    {
        return \sprintf('%s metadata lookup (%s)', config('app.name'), config('app.url'));
    }
}
