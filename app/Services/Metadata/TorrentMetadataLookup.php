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
use App\Exceptions\MetadataNotFoundException;
use App\Exceptions\MetadataProviderUnavailableException;
use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Services\GoogleBooks\GoogleBooksClient;
use App\Services\Igdb\IgdbClient;
use App\Services\MusicBrainz\MusicBrainzClient;
use App\Services\OpenLibrary\OpenLibraryClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Resolves complete provider metadata with a readable pre-upload preview
 * for the torrent upload form's "fetch metadata" button.
 *
 * This performs live, synchronous, read-only lookups against already
 * configured external providers. It never persists anything and never
 * dispatches jobs.
 *
 * @phpstan-type ProviderResult array{
 *     title: string,
 *     description: string,
 *     description_language: ?string,
 *     source: string,
 *     source_url: ?string,
 *     cover_url: ?string,
 *     identifiers: array<string, string>,
 *     warning: ?string,
 *     raw: array<string, mixed>,
 * }
 * @phpstan-type LookupResult ProviderResult&array{details: list<array{key: string, label: string, value: string}>}
 */
final class TorrentMetadataLookup
{
    public function __construct(
        private readonly IgdbClient $igdb,
        private readonly MusicBrainzClient $musicBrainz,
        private readonly OpenLibraryClient $openLibrary,
        private readonly GoogleBooksClient $googleBooks,
    ) {
    }

    /**
     * @return LookupResult
     */
    public function lookup(Category $category, string $identifier): array
    {
        $identifier = trim($identifier);

        if ($identifier === '') {
            throw new InvalidMetadataIdentifierException(__('metadata.errors.identifier-required'));
        }

        $kind = UploadKinds::categoryKind($category);

        $result = match ($kind) {
            'movie' => $this->lookupTmdb($identifier, 'movie'),
            'tv'    => $this->lookupTmdb($identifier, 'tv'),
            'game'  => $this->lookupGame($identifier),
            'music' => $this->lookupMusic($identifier),
            'book'  => $this->lookupBook($identifier),
            default => throw new InvalidMetadataIdentifierException(__('metadata.errors.category-not-lookupable')),
        };

        $result['details'] = MetadataDetails::forSource($result['source'], $result['raw']);

        return $result;
    }

    /** @return ProviderResult */
    private function lookupTmdb(string $identifier, string $type): array
    {
        $id = $this->positiveIntOrFail($identifier);
        $key = config('api-keys.tmdb');
        if (!\is_string($key) || $key === '') {
            throw new MetadataProviderUnavailableException(__('metadata.errors.tmdb-not-configured'));
        }

        $fetch = static function (string $language) use ($id, $type, $key): array {
            $response = Http::acceptJson()->connectTimeout(3)->timeout(8)
                ->get("https://api.themoviedb.org/3/{$type}/{$id}", [
                    'api_key' => $key, 'language' => $language,
                    'append_to_response' => $type === 'movie'
                        ? 'videos,images,credits,external_ids,keywords,recommendations,alternative_titles,release_dates'
                        : 'videos,images,aggregate_credits,external_ids,keywords,recommendations,alternative_titles,content_ratings',
                ]);
            if ($response->notFound()) {
                throw new MetadataNotFoundException(__('metadata.errors.not-found'));
            }
            $data = $response->throw()->json();
            if (!\is_array($data)) {
                throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
            }
            return $data;
        };

        try {
            $data = $fetch('cs-CZ');
            $titleKey = $type === 'movie' ? 'title' : 'name';
            if (empty($data[$titleKey])) {
                throw new MetadataNotFoundException(__('metadata.errors.not-found'));
            }
            $description = $data['overview'] ?? '';
            $language = $description !== '' ? 'cs' : null;
            $warning = null;
            if ($description === '') {
                $english = $fetch('en-US');
                $data['english_fallback'] = $english;
                $description = $english['overview'] ?? '';
                if ($description !== '') {
                    $language = 'en';
                    $warning = __('metadata.warnings.tmdb-english-fallback');
                }
            }
        } catch (RequestException|ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        }

        return [
            'title' => $data[$titleKey],
            'description' => html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'description_language' => $language,
            'source' => 'TMDB',
            'raw' => $data,
            'source_url' => "https://www.themoviedb.org/{$type}/{$id}",
            'cover_url' => empty($data['poster_path']) ? null : 'https://image.tmdb.org/t/p/original'.$data['poster_path'],
            'identifiers' => [($type === 'movie' ? 'tmdb_movie_id' : 'tmdb_tv_id') => (string) $id],
            'warning' => $warning,
        ];
    }

    /**
     * @return ProviderResult
     */
    private function lookupGame(string $identifier): array
    {
        $id = $this->positiveIntOrFail($identifier);

        $clientId = config('igdb.credentials.client_id');
        $clientSecret = config('igdb.credentials.client_secret');

        if (!\is_string($clientId) || $clientId === '' || !\is_string($clientSecret) || $clientSecret === '') {
            throw new MetadataProviderUnavailableException(__('metadata.errors.igdb-not-configured'));
        }

        try {
            $game = $this->igdb->game($id);
        } catch (RequestException|ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (RuntimeException $exception) {
            // IgdbClient::game() throws a RuntimeException (rather than an
            // HTTP exception) specifically when the game id doesn't exist.
            throw new MetadataNotFoundException(__('metadata.errors.not-found'));
        }

        if (!isset($game['name']) || !\is_string($game['name'])) {
            throw new MetadataNotFoundException(__('metadata.errors.not-found'));
        }

        $coverImageId = $game['cover']['image_id'] ?? null;

        return [
            'title'                => $game['name'],
            'description'          => \is_string($game['summary'] ?? null) ? $game['summary'] : '',
            'description_language' => \is_string($game['summary'] ?? null) && $game['summary'] !== '' ? 'en' : null,
            'source'               => 'IGDB',
            'raw'                  => $game,
            'source_url'           => \is_string($game['url'] ?? null) ? $game['url'] : null,
            'cover_url'            => \is_string($coverImageId) ? "https://images.igdb.com/igdb/image/upload/t_cover_big/{$coverImageId}.jpg" : null,
            'identifiers'          => ['igdb' => (string) $id],
            'warning'              => null,
        ];
    }

    /**
     * @return ProviderResult
     */
    private function lookupMusic(string $identifier): array
    {
        if (!$this->isUuid($identifier)) {
            throw new InvalidMetadataIdentifierException(__('metadata.errors.invalid-musicbrainz-id'));
        }

        try {
            $metadata = $this->musicBrainz->lookup($identifier);
            $release = $metadata['data'];
            $entity = $metadata['entity'];
        } catch (RequestException $exception) {
            if ($exception->response->notFound()) {
                throw new MetadataNotFoundException(__('metadata.errors.not-found'));
            }

            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (RuntimeException) {
            throw new MetadataNotFoundException(__('metadata.errors.not-found'));
        }

        $artists = collect($release['artist-credit'] ?? [])
            ->pluck('name')
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');

        $title = $release['title'];

        if ($artists !== '') {
            $title = "{$artists} - {$title}";
        }

        return [
            'title'                => $title,
            'description'          => '',
            'description_language' => null,
            'source'               => 'MusicBrainz',
            'raw'                  => $release,
            'source_url'           => "https://musicbrainz.org/{$entity}/{$release['id']}",
            'cover_url'            => "https://coverartarchive.org/{$entity}/{$release['id']}/front",
            'identifiers'          => ['musicbrainz_id' => $release['id']],
            'warning'              => null,
        ];
    }

    /**
     * @return ProviderResult
     */
    private function lookupBook(string $identifier): array
    {
        if (preg_match('/^OL\d+M$/i', $identifier) === 1) {
            return $this->lookupBookByOpenLibraryEdition($identifier);
        }

        $isbn = $this->normalizeIsbnOrFail($identifier);

        return $this->lookupBookByIsbn($isbn, $identifier);
    }

    /**
     * @return ProviderResult
     */
    private function lookupBookByOpenLibraryEdition(string $identifier): array
    {
        try {
            $book = $this->openLibrary->edition($identifier);
        } catch (RequestException $exception) {
            if ($exception->response->notFound()) {
                throw new MetadataNotFoundException(__('metadata.errors.not-found'));
            }

            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (ConnectionException) {
            throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
        } catch (RuntimeException) {
            throw new MetadataNotFoundException(__('metadata.errors.not-found'));
        }

        $olid = basename($book['key']);
        $isCzech = $this->openLibraryEditionIsCzech($book);
        $description = $this->openLibraryDescription($book);

        return [
            'title'                => $book['title'],
            'description'          => $isCzech ? $description : '',
            'description_language' => $isCzech && $description !== '' ? 'cs' : null,
            'source'               => 'Open Library',
            'source_url'           => "https://openlibrary.org/books/{$olid}",
            'raw'                  => $book,
            'cover_url'            => "https://covers.openlibrary.org/b/olid/{$olid}-L.jpg",
            'identifiers'          => ['open_library_edition_id' => $olid],
            'warning'              => $isCzech
                ? ($description === '' ? __('metadata.warnings.book-no-description') : null)
                : __('metadata.warnings.book-no-czech-description'),
        ];
    }

    /**
     * @return ProviderResult
     */
    private function lookupBookByIsbn(string $isbn, string $originalIdentifier): array
    {
        $googleVolume = null;
        $googleFailed = false;

        try {
            $googleVolume = $this->googleBooks->findByIsbn($isbn);
        } catch (RequestException|ConnectionException) {
            $googleFailed = true;
        }

        if (\is_array($googleVolume) && ($googleVolume['language'] ?? null) === 'cs'
            && trim(strip_tags((string) ($googleVolume['description'] ?? ''))) !== '') {
            return [
                'title'                => \is_string($googleVolume['title'] ?? null) ? $googleVolume['title'] : $isbn,
                'description'          => \is_string($googleVolume['description'] ?? null) ? strip_tags($googleVolume['description']) : '',
                'description_language' => 'cs',
                'source'               => 'Google Books',
                'raw'                  => $googleVolume,
                'source_url'           => \is_string($googleVolume['previewLink'] ?? null) ? $googleVolume['previewLink'] : ($googleVolume['infoLink'] ?? null),
                'cover_url'            => $this->googleCoverUrl($googleVolume),
                'identifiers'          => ['open_library_edition_id' => $originalIdentifier],
                'warning'              => null,
            ];
        }

        // No exact, Czech-language Google Books match. Try a verified Czech
        // Open Library edition for the same ISBN before giving up on a
        // Czech description.
        $openLibraryBook = null;

        try {
            $openLibraryBook = $this->openLibrary->edition($isbn);
        } catch (RequestException|ConnectionException|RuntimeException) {
            $openLibraryBook = null;
        }

        if (\is_array($openLibraryBook) && $this->openLibraryEditionIsCzech($openLibraryBook)) {
            $description = $this->openLibraryDescription($openLibraryBook);

            if ($description !== '') {
                $olid = basename($openLibraryBook['key']);

                return [
                    'title'                => \is_string($googleVolume['title'] ?? null) ? $googleVolume['title'] : $openLibraryBook['title'],
                    'description'          => $description,
                    'description_language' => 'cs',
                    'source'               => 'Open Library',
                    'raw'                  => $openLibraryBook + ['lookup_sources' => ['google_books' => $googleVolume]],
                    'source_url'           => "https://openlibrary.org/books/{$olid}",
                    'cover_url'            => $this->googleCoverUrl($googleVolume) ?? "https://covers.openlibrary.org/b/olid/{$olid}-L.jpg",
                    'identifiers'          => ['open_library_edition_id' => $originalIdentifier],
                    'warning'              => null,
                ];
            }
        }

        if (!\is_array($googleVolume) && !\is_array($openLibraryBook)) {
            if ($googleFailed) {
                throw new MetadataProviderUnavailableException(__('metadata.errors.provider-unavailable'));
            }

            throw new MetadataNotFoundException(__('metadata.errors.not-found'));
        }

        // We have a title/cover (from Google and/or Open Library) but no
        // provider has a verified Czech description for this ISBN.
        $title = \is_string($googleVolume['title'] ?? null)
            ? $googleVolume['title']
            : (\is_array($openLibraryBook) ? $openLibraryBook['title'] : $isbn);

        $fallbackDescription = \is_string($googleVolume['description'] ?? null) ? strip_tags($googleVolume['description']) : '';
        $fallbackLanguage = \is_string($googleVolume['language'] ?? null) && $fallbackDescription !== '' ? $googleVolume['language'] : null;

        return [
            'title'                => $title,
            'description'          => $fallbackDescription,
            'description_language' => $fallbackLanguage,
            'source'               => \is_array($googleVolume) ? 'Google Books' : 'Open Library',
            'raw'                  => \is_array($googleVolume)
                ? $googleVolume + ['lookup_sources' => ['open_library' => $openLibraryBook]]
                : $openLibraryBook,
            'source_url'           => \is_string($googleVolume['previewLink'] ?? null) ? $googleVolume['previewLink'] : ($googleVolume['infoLink'] ?? (isset($openLibraryBook['key']) ? 'https://openlibrary.org'.$openLibraryBook['key'] : null)),
            'cover_url'            => $this->googleCoverUrl($googleVolume) ?? (isset($openLibraryBook['key']) ? 'https://covers.openlibrary.org/b/olid/'.basename($openLibraryBook['key']).'-L.jpg' : null),
            'identifiers'          => ['open_library_edition_id' => $originalIdentifier],
            'warning'              => __('metadata.warnings.book-no-czech-description'),
        ];
    }

    /**
     * @param array<string, mixed> $book Open Library edition payload.
     */
    private function openLibraryEditionIsCzech(array $book): bool
    {
        $languages = $book['languages'] ?? [];

        if (!\is_array($languages)) {
            return false;
        }

        foreach ($languages as $language) {
            if (\is_array($language) && \is_string($language['key'] ?? null) && str_ends_with($language['key'], '/cze')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $book Open Library edition payload.
     */
    private function openLibraryDescription(array $book): string
    {
        $description = $book['description'] ?? null;

        if (\is_string($description)) {
            return strip_tags($description);
        }

        if (\is_array($description) && \is_string($description['value'] ?? null)) {
            return strip_tags($description['value']);
        }

        return '';
    }

    /**
     * @param null|array<string, mixed> $googleVolume
     */
    private function googleCoverUrl(?array $googleVolume): ?string
    {
        $thumbnail = $googleVolume['imageLinks']['thumbnail'] ?? $googleVolume['imageLinks']['smallThumbnail'] ?? null;

        if (!\is_string($thumbnail) || $thumbnail === '') {
            return null;
        }

        return preg_replace('/^http:/', 'https:', $thumbnail);
    }

    private function positiveIntOrFail(string $identifier): int
    {
        if (preg_match('/^\d+$/', $identifier) !== 1 || (int) $identifier < 1) {
            throw new InvalidMetadataIdentifierException(__('metadata.errors.invalid-numeric-id'));
        }

        return (int) $identifier;
    }

    private function isUuid(string $identifier): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier) === 1;
    }

    /**
     * Validate and normalize an ISBN-10 or ISBN-13 (hyphens/spaces allowed)
     * via its checksum. Returns the normalized (digits + optional trailing
     * X, no separators) ISBN.
     */
    private function normalizeIsbnOrFail(string $identifier): string
    {
        $normalized = strtoupper(preg_replace('/[\s-]/', '', $identifier) ?? '');

        if (preg_match('/^\d{9}[\dX]$/', $normalized) === 1 && $this->isValidIsbn10($normalized)) {
            return $normalized;
        }

        if (preg_match('/^\d{13}$/', $normalized) === 1 && $this->isValidIsbn13($normalized)) {
            return $normalized;
        }

        throw new InvalidMetadataIdentifierException(__('metadata.errors.invalid-isbn'));
    }

    private function isValidIsbn10(string $isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $char = $isbn[$i];
            $value = $char === 'X' ? 10 : (int) $char;
            $sum += $value * (10 - $i);
        }

        return $sum % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return $sum % 10 === 0;
    }
}
