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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Services\Metadata;

use App\Exceptions\InvalidMetadataIdentifierException;
use App\Exceptions\MetadataProviderUnavailableException;
use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Services\Igdb\IgdbClient;
use App\Services\MusicBrainz\MusicBrainzClient;
use App\Services\OpenLibrary\OpenLibraryClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Live, synchronous, read-only title/text search across providers for the
 * upload form's "search" button. Returns bounded candidate lists only: it
 * never selects, loads, or persists a result on its own. Picking one of the
 * returned identifiers and running it through the existing
 * {@see TorrentMetadataLookup} (or {@see musicReleases()} for a specific
 * music edition) is a separate, explicit step.
 *
 * @phpstan-type SearchResult array{
 *     identifier: string,
 *     entity: string,
 *     title: string,
 *     year: ?string,
 *     artists: ?string,
 *     cover_url: ?string,
 *     subtitle: ?string,
 * }
 * @phpstan-type ReleaseResult array{
 *     identifier: string,
 *     entity: 'release',
 *     title: string,
 *     year: ?string,
 *     artists: ?string,
 *     country: ?string,
 *     format: ?string,
 *     track_count: ?int,
 * }
 */
final class TorrentMetadataSearch
{
    private const int MAX_RESULTS = 12;

    public function __construct(
        private readonly IgdbClient $igdb,
        private readonly MusicBrainzClient $musicBrainz,
        private readonly OpenLibraryClient $openLibrary,
    ) {
    }

    /**
     * @return list<SearchResult>
     */
    public function search(Category $category, string $query): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2 || mb_strlen($query) > 200) {
            throw new InvalidMetadataIdentifierException(__('discovery.errors.query-length'));
        }

        $kind = UploadKinds::categoryKind($category);

        return match ($kind) {
            'movie' => $this->searchTmdb($query, 'movie'),
            'tv'    => $this->searchTmdb($query, 'tv'),
            'game'  => $this->searchIgdb($query),
            'music' => $this->searchMusic($query),
            'book'  => $this->searchBooks($query),
            default => throw new InvalidMetadataIdentifierException(__('discovery.errors.category-not-searchable')),
        };
    }

    /**
     * Bounded, paginated list of releases (editions) belonging to exactly
     * one, explicitly chosen release-group. Never picks a release itself.
     *
     * @return array{results: list<ReleaseResult>, next_offset: ?int}
     */
    public function musicReleases(string $releaseGroupId, int $offset): array
    {
        if (!$this->isUuid($releaseGroupId)) {
            throw new InvalidMetadataIdentifierException(__('discovery.errors.invalid-release-group-id'));
        }

        try {
            $page = $this->musicBrainz->browseReleases($releaseGroupId, $offset, self::MAX_RESULTS);
        } catch (RequestException $exception) {
            if ($exception->response->notFound()) {
                return ['results' => [], 'next_offset' => null];
            }

            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (ConnectionException|RuntimeException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        $releases = $page['releases'];
        $total = $page['release-count'];

        $results = collect($releases)
            ->filter(fn (mixed $release): bool => \is_array($release) && isset($release['id'], $release['title']) && \is_string($release['id']) && \is_string($release['title']))
            ->map(function (array $release): array {
                $artists = collect($release['artist-credit'] ?? [])
                    ->filter(fn (mixed $credit): bool => \is_array($credit))
                    ->pluck('name')
                    ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
                    ->join(', ');

                $media = collect($release['media'] ?? [])->filter(fn (mixed $medium): bool => \is_array($medium));

                $formats = $media
                    ->pluck('format')
                    ->filter(fn (mixed $format): bool => \is_string($format) && $format !== '')
                    ->unique()
                    ->values();

                $trackCount = (int) $media->sum(fn (array $medium): int => \is_int($medium['track-count'] ?? null) ? $medium['track-count'] : 0);

                $date = $release['date'] ?? null;

                return [
                    'identifier'  => $release['id'],
                    'entity'      => 'release',
                    'title'       => $release['title'],
                    'year'        => \is_string($date) && $date !== '' ? substr($date, 0, 4) : null,
                    'artists'     => $artists !== '' ? $artists : null,
                    'country'     => \is_string($release['country'] ?? null) ? $release['country'] : null,
                    'format'      => $formats->isEmpty() ? null : $formats->implode(', '),
                    'track_count' => $trackCount > 0 ? $trackCount : null,
                ];
            })
            ->values()
            ->all();

        $nextOffset = ($offset + \count($releases)) < $total ? $offset + \count($releases) : null;

        return ['results' => $results, 'next_offset' => $nextOffset];
    }

    /**
     * @return list<SearchResult>
     */
    private function searchTmdb(string $query, string $type): array
    {
        $key = config('api-keys.tmdb');

        if (!\is_string($key) || $key === '') {
            throw new MetadataProviderUnavailableException(__('metadata.errors.tmdb-not-configured'));
        }

        try {
            $data = Http::acceptJson()->connectTimeout(3)->timeout(8)
                ->get("https://api.themoviedb.org/3/search/{$type}", [
                    'api_key'       => $key,
                    'query'         => $query,
                    'language'      => config('app.meta_locale', 'en-US'),
                    'include_adult' => false,
                ])
                ->throw()
                ->json();
        } catch (RequestException|ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        if (!\is_array($data) || !isset($data['results']) || !\is_array($data['results'])) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        $titleKey = $type === 'movie' ? 'title' : 'name';
        $originalTitleKey = $type === 'movie' ? 'original_title' : 'original_name';
        $dateKey = $type === 'movie' ? 'release_date' : 'first_air_date';

        return collect($data['results'])
            ->filter(fn (mixed $item): bool => \is_array($item) && isset($item['id'], $item[$titleKey]) && \is_string($item[$titleKey]) && $item[$titleKey] !== '')
            ->take(self::MAX_RESULTS)
            ->map(function (array $item) use ($type, $titleKey, $originalTitleKey, $dateKey): array {
                $title = $item[$titleKey];
                $original = \is_string($item[$originalTitleKey] ?? null) ? $item[$originalTitleKey] : null;
                $date = \is_string($item[$dateKey] ?? null) ? $item[$dateKey] : null;

                return [
                    'identifier' => (string) $item['id'],
                    'entity'     => $type,
                    'title'      => $title,
                    'year'       => $date !== null && $date !== '' ? substr($date, 0, 4) : null,
                    'artists'    => null,
                    'cover_url'  => !empty($item['poster_path']) && \is_string($item['poster_path']) ? 'https://image.tmdb.org/t/p/w342'.$item['poster_path'] : null,
                    'subtitle'   => ($original !== null && $original !== $title) ? $original : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<SearchResult>
     */
    private function searchIgdb(string $query): array
    {
        $clientId = config('igdb.credentials.client_id');
        $clientSecret = config('igdb.credentials.client_secret');

        if (!\is_string($clientId) || $clientId === '' || !\is_string($clientSecret) || $clientSecret === '') {
            throw new MetadataProviderUnavailableException(__('metadata.errors.igdb-not-configured'));
        }

        try {
            $games = $this->igdb->search($query, self::MAX_RESULTS);
        } catch (RequestException|ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        return collect($games)
            ->filter(fn (mixed $game): bool => \is_array($game) && isset($game['id'], $game['name']) && \is_string($game['name']) && $game['name'] !== '')
            ->take(self::MAX_RESULTS)
            ->map(function (array $game): array {
                $coverImageId = $game['cover']['image_id'] ?? null;
                $year = \is_int($game['first_release_date'] ?? null) ? gmdate('Y', $game['first_release_date']) : null;

                return [
                    'identifier' => (string) $game['id'],
                    'entity'     => 'game',
                    'title'      => $game['name'],
                    'year'       => $year,
                    'artists'    => null,
                    'cover_url'  => \is_string($coverImageId) ? "https://images.igdb.com/igdb/image/upload/t_cover_big/{$coverImageId}.jpg" : null,
                    'subtitle'   => null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<SearchResult>
     */
    private function searchMusic(string $query): array
    {
        try {
            $groups = $this->musicBrainz->searchReleaseGroups($query, self::MAX_RESULTS);
        } catch (RequestException|ConnectionException|RuntimeException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        return collect($groups)
            ->filter(fn (mixed $group): bool => \is_array($group) && isset($group['id'], $group['title']) && \is_string($group['id']) && \is_string($group['title']))
            ->take(self::MAX_RESULTS)
            ->map(function (array $group): array {
                $artists = collect($group['artist-credit'] ?? [])
                    ->filter(fn (mixed $credit): bool => \is_array($credit))
                    ->pluck('name')
                    ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
                    ->join(', ');

                $date = $group['first-release-date'] ?? null;

                return [
                    'identifier' => $group['id'],
                    'entity'     => 'release-group',
                    'title'      => $group['title'],
                    'year'       => \is_string($date) && $date !== '' ? substr($date, 0, 4) : null,
                    'artists'    => $artists !== '' ? $artists : null,
                    'cover_url'  => "https://coverartarchive.org/release-group/{$group['id']}/front",
                    'subtitle'   => \is_string($group['primary-type'] ?? null) ? $group['primary-type'] : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<SearchResult>
     */
    private function searchBooks(string $query): array
    {
        try {
            $docs = $this->openLibrary->search($query, self::MAX_RESULTS);
        } catch (RequestException|ConnectionException|RuntimeException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        return collect($docs)
            ->filter(function (mixed $doc): bool {
                if (!\is_array($doc) || !isset($doc['title']) || !\is_string($doc['title']) || $doc['title'] === '') {
                    return false;
                }

                $editionKeys = $doc['edition_key'] ?? null;

                // Open Library work ids alone are not a usable lookup
                // target: the existing lookup only understands concrete
                // editions/ISBNs. A work without any edition id can't be
                // turned into a real result here.
                return \is_array($editionKeys) && isset($editionKeys[0]) && \is_string($editionKeys[0]) && $editionKeys[0] !== '';
            })
            ->take(self::MAX_RESULTS)
            ->map(function (array $doc): array {
                $authors = collect($doc['author_name'] ?? [])
                    ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
                    ->join(', ');

                return [
                    'identifier' => $doc['edition_key'][0],
                    'entity'     => 'book',
                    'title'      => $doc['title'],
                    'year'       => \is_int($doc['first_publish_year'] ?? null) ? (string) $doc['first_publish_year'] : null,
                    'artists'    => $authors !== '' ? $authors : null,
                    'cover_url'  => \is_int($doc['cover_i'] ?? null) ? "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-M.jpg" : null,
                    'subtitle'   => null,
                ];
            })
            ->values()
            ->all();
    }

    private function isUuid(string $identifier): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier) === 1;
    }
}
