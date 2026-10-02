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

namespace App\Services\Media;

use App\Helpers\UploadKinds;
use App\Models\Category;
use App\Models\IgdbGame;
use App\Models\MediaWork;
use App\Models\TmdbMovie;
use App\Models\TmdbTv;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Resolves and maintains the canonical {@see MediaWork} a Torrent belongs
 * to, independent of category: one Work groups every quality/edition/scope
 * variant of the same abstract title so the catalogue shows a single entry
 * per title ("Ano, šéfe!" example) while every individual torrent keeps its
 * own swarm stats, scope (season/episode/package) and download link.
 *
 * Identity is namespaced `{kind}:{source}:{id}` from a trusted provider id
 * (TMDB movie vs tv, IGDB, MusicBrainz release-group, Open Library work) so
 * two different kinds never collide on the same numeric id, and two
 * different titles are never merged just because they share a filename.
 * Content with no provider id (or not yet identified) gets a unique
 * provisional identity scoped to the uploading torrent; it is only ever
 * merged into another Work by explicit, privacy-checked user choice
 * ({@see attach()} with `$selectedWorkId`) or automatically once trusted
 * metadata resolves a real provider id ({@see sync()}).
 *
 * @phpstan-type Lookup array{
 *     title?: string,
 *     description?: string,
 *     source?: string,
 *     source_url?: ?string,
 *     cover_url?: ?string,
 *     identifiers?: array<string, string>,
 *     raw?: array<string, mixed>,
 * }
 */
final class MediaWorkCatalog
{
    /**
     * Fields apply() is allowed to selectively refresh.
     *
     * @var list<string>
     */
    private const array APPLICABLE_FIELDS = ['title', 'description', 'cover_url', 'raw'];

    /**
     * Resolve (creating if necessary) the canonical Work a torrent belongs
     * to and link the torrent to it. Safe to call multiple times.
     *
     * @param Lookup|null $lookup a trusted provider snapshot (e.g. from the
     *                            upload's MetadataSelectionStore token), never raw client input
     * @param int|null    $selectedWorkId an existing Work the uploader explicitly chose
     *                                    (e.g. "add quality variant to this title" for provider-less content)
     */
    public function attach(Torrent $torrent, ?array $lookup = null, ?int $selectedWorkId = null): MediaWork
    {
        $this->validateSelection($torrent, $lookup, $selectedWorkId);

        $identity = $this->deriveIdentity($torrent, $lookup);

        if ($selectedWorkId !== null) {
            /** @var MediaWork $work */
            $work = MediaWork::query()->findOrFail($selectedWorkId);
        } else {
            $work = $this->findOrCreateWork($identity, $torrent->getAttribute('name'));
        }

        if ($lookup !== null) {
            $work = $this->apply($work, $lookup);
        }

        $torrent->updateQuietly(['media_work_id' => $work->id]);

        return $work;
    }

    /**
     * Re-resolve a persisted torrent's Work using only locally stored
     * provider models / TorrentMetadata (no outbound HTTP). Intended to run
     * after a metadata job persists provider data, so grouping never depends
     * on the uploader having submitted MediaInfo. May upgrade a previously
     * provisional (provider-less) attachment into the correct canonical
     * Work once real metadata becomes available, without touching any other
     * torrent's data.
     */
    public function sync(Torrent $torrent): MediaWork
    {
        $identity = $this->deriveIdentity($torrent, null);

        $currentWorkId = $torrent->getAttribute('media_work_id');

        if ($identity['provisional']) {
            // No canonical provider id resolvable from stored data. Never
            // reassign an already-resolved attachment onto a fresh,
            // torrent-scoped provisional identity: that would silently
            // undo an explicit user merge onto a shared provider-less Work
            // (or any other prior attachment) every time sync() runs.
            if ($currentWorkId !== null) {
                $current = MediaWork::query()->find($currentWorkId);

                if ($current !== null) {
                    return $current;
                }
            }

            $work = $this->findOrCreateWork($identity, $torrent->getAttribute('name'));
            $torrent->updateQuietly(['media_work_id' => $work->id]);

            return $work;
        }

        if ($currentWorkId !== null) {
            /** @var MediaWork|null $current */
            $current = MediaWork::query()->find($currentWorkId);

            if ($current !== null && $current->identity_key === $identity['identityKey']) {
                // Already attached to the correct canonical Work; only
                // refresh metadata if we have a real payload to refresh from.
                $lookup = $this->resolveLookupFromStorage($identity, $torrent);

                if ($lookup !== null) {
                    $current = $this->apply($current, $lookup);
                }

                return $current;
            }
        }

        $work = $this->findOrCreateWork($identity, $torrent->getAttribute('name'));

        $lookup = $this->resolveLookupFromStorage($identity, $torrent);

        if ($lookup !== null) {
            $work = $this->apply($work, $lookup);
        }

        $torrent->updateQuietly(['media_work_id' => $work->id]);

        return $work;
    }

    /**
     * Apply trusted lookup metadata to a Work. `$fields` null means a normal
     * full refresh; an explicit subset (from `self::APPLICABLE_FIELDS`)
     * allows a reviewed/staff-selected partial update. Never relocates a
     * Work to an unrelated provider identity: if the lookup's own canonical
     * id can be determined and it doesn't match `$work`'s, this throws
     * rather than silently overwriting the wrong title.
     *
     * @param Lookup       $lookup
     * @param list<string>|null $fields
     */
    public function apply(MediaWork $work, array $lookup, ?array $fields = null): MediaWork
    {
        $canonical = $this->canonicalIdentityFromLookup($work->kind, $lookup);

        if ($canonical !== null && $canonical['identityKey'] !== $work->identity_key) {
            throw new InvalidArgumentException(
                "Refusing to apply lookup metadata identified as [{$canonical['identityKey']}] to Work [{$work->identity_key}].",
            );
        }

        $selected = $fields === null
            ? self::APPLICABLE_FIELDS
            : array_values(array_intersect(self::APPLICABLE_FIELDS, $fields));

        $attributes = [];

        foreach ($selected as $field) {
            if (!\array_key_exists($field, $lookup)) {
                continue;
            }

            $attributes[$field] = $lookup[$field];
        }

        if ($canonical !== null) {
            $attributes['source'] = $canonical['source'];
            $attributes['source_id'] = $canonical['sourceId'];
        }

        if (isset($lookup['source_url'])) {
            $attributes['source_url'] = $lookup['source_url'];
        }

        if (\in_array('raw', $selected, true) && \array_key_exists('raw', $lookup)) {
            $attributes['facets'] = $this->computeFacets($work->kind, $lookup['raw'] ?? []);
        }

        $attributes['metadata_updated_at'] = now();
        $attributes['metadata_error'] = null;

        $work->update($attributes);

        return $work->refresh();
    }

    /**
     * Pure, read-only check that a torrent (saved or not yet saved) may be
     * attached to `$selectedWorkId` and/or `$lookup` without contradicting
     * an already-known provider identity. Never persists anything, so it is
     * safe to call from request validation on an unsaved Torrent instance
     * carrying only `category_id` and the normalized identifier attributes
     * (`tmdb_movie_id`, `tmdb_tv_id`, `igdb`, `musicbrainz_id`,
     * `open_library_edition_id`).
     *
     * @param Lookup|null $lookup
     *
     * @throws ValidationException keyed `media_work_id` (metadata.errors.work-mismatch) when the
     *                              selected Work's kind/identity contradicts the torrent's own provider
     *                              identity, or `metadata_selection_token`
     *                              (metadata.errors.selection-expired) when a music/book identifier was
     *                              typed but cannot be verified without a fresh provider lookup
     */
    public function validateSelection(Torrent $torrent, ?array $lookup = null, ?int $selectedWorkId = null): void
    {
        if ($selectedWorkId === null && $lookup === null) {
            return;
        }

        $identity = $this->deriveIdentity($torrent, $lookup);

        if ($selectedWorkId === null) {
            // A lookup without an explicit selection is only used to seed a
            // brand new/refreshed canonical identity; there is nothing
            // existing to contradict.
            return;
        }

        $work = MediaWork::query()->find($selectedWorkId);

        if ($work === null || $work->kind !== $identity['kind']) {
            throw ValidationException::withMessages([
                'media_work_id' => [__('metadata.errors.work-mismatch')],
            ]);
        }

        if (!$identity['provisional']) {
            if ($work->identity_key !== $identity['identityKey']) {
                throw ValidationException::withMessages([
                    'media_work_id' => [__('metadata.errors.work-mismatch')],
                ]);
            }
        } else {
            // The torrent's own identity could not be canonically verified
            // in-process. If that's because a music/book identifier was
            // typed without ever resolving a trusted lookup/stored metadata
            // for it, don't silently trust the raw string as proof it
            // matches the selected Work; demand a fresh, verifiable
            // selection instead.
            $unverifiedProviderInput = $lookup === null && (
                filled($torrent->getAttribute('musicbrainz_id'))
                || filled($torrent->getAttribute('open_library_edition_id'))
            );

            if ($unverifiedProviderInput) {
                throw ValidationException::withMessages([
                    'metadata_selection_token' => [__('metadata.errors.selection-expired')],
                ]);
            }
        }

        // An explicitly chosen Work -- canonical match or genuinely
        // provider-less content alike -- must already be visible to the
        // current user, so a tampered/guessed id can't merge new content
        // into something hidden from everyone.
        if (!$work->torrents()->exists()) {
            throw ValidationException::withMessages([
                'media_work_id' => [__('metadata.errors.work-mismatch')],
            ]);
        }
    }

    /**
     * @return array{kind: string, source: ?string, sourceId: ?string, identityKey: string, provisional: bool}
     */
    private function deriveIdentity(Torrent $torrent, ?array $lookup): array
    {
        $category = $torrent->relationLoaded('category') ? $torrent->category : Category::query()->find($torrent->getAttribute('category_id'));
        $kind = $category !== null ? UploadKinds::categoryKind($category) : 'no';

        $identifiers = $lookup !== null
            ? $this->identifiersFromLookup($kind, $lookup)
            : match ($kind) {
                'movie' => ['tmdb_movie_id' => $torrent->getAttribute('tmdb_movie_id')],
                'tv'    => ['tmdb_tv_id' => $torrent->getAttribute('tmdb_tv_id')],
                'game'  => ['igdb' => $torrent->getAttribute('igdb')],
                'book'  => $this->storedBookIdentifiers($torrent),
                default => [],
            };

        $raw = $lookup['raw'] ?? $this->storedRawFor($kind, $torrent);

        $canonical = $this->canonicalIdentity($kind, $raw, $identifiers);

        if ($canonical === null) {
            return [
                'kind'        => $kind,
                'source'      => null,
                'sourceId'    => null,
                'identityKey' => \sprintf('%s:torrent:%s', $kind, $torrent->getAttribute('id') ?? spl_object_id($torrent)),
                'provisional' => true,
            ];
        }

        return [
            'kind'        => $kind,
            'source'      => $canonical['source'],
            'sourceId'    => $canonical['sourceId'],
            'identityKey' => $canonical['identityKey'],
            'provisional' => false,
        ];
    }

    /**
     * @param array<string, mixed> $identifiers
     *
     * @return array{source: string, sourceId: string, identityKey: string}|null
     */
    private function canonicalIdentity(string $kind, ?array $raw, array $identifiers): ?array
    {
        return match ($kind) {
            'movie' => $this->numericIdentity('movie', 'tmdb', $identifiers['tmdb_movie_id'] ?? null),
            'tv'    => $this->numericIdentity('tv', 'tmdb', $identifiers['tmdb_tv_id'] ?? null),
            'game'  => $this->numericIdentity('game', 'igdb', $identifiers['igdb'] ?? null),
            'music' => $this->musicIdentity($raw),
            'book'  => $this->bookIdentity($raw, $identifiers),
            default => null,
        };
    }

    /**
     * @return array{source: string, sourceId: string, identityKey: string}|null
     */
    private function numericIdentity(string $kind, string $source, mixed $rawId): ?array
    {
        if (!is_numeric($rawId)) {
            return null;
        }

        $id = (int) $rawId;

        if ($id <= 0) {
            return null;
        }

        return ['source' => $source, 'sourceId' => (string) $id, 'identityKey' => "{$kind}:{$source}:{$id}"];
    }

    /**
     * MusicBrainz release-group id: either embedded on a `release` payload
     * (`release-group.id`) or, when the identity we already resolved to a
     * `release-group` entity directly, the record's own id.
     *
     * @param array<string, mixed>|null $raw
     *
     * @return array{source: string, sourceId: string, identityKey: string}|null
     */
    private function musicIdentity(?array $raw): ?array
    {
        if ($raw === null) {
            return null;
        }

        $groupId = $raw['release-group']['id'] ?? $raw['id'] ?? null;

        if (!\is_string($groupId) || $groupId === '') {
            return null;
        }

        return ['source' => 'musicbrainz', 'sourceId' => $groupId, 'identityKey' => "music:musicbrainz:{$groupId}"];
    }

    /**
     * Grouping identity prefers, in order: the Open Library work id when the
     * edition payload actually references one (`works[0].key`), so
     * translations/reprints of the same book merge; then a Google Books
     * volume's own stable id (`google_books_item.id`) when the match came
     * from Google, since that id is the same regardless of which ISBN/OLID
     * search led here -- deriving it from raw (present both at attach time,
     * from the trusted lookup snapshot, and later at sync time, from the
     * exact same payload stored on TorrentMetadata) keeps attach() and
     * sync() agreeing instead of splitting the same book into two Works.
     * `source`/`sourceId` always stay a real, edition-level identifier
     * (Open Library edition id, ISBN, or Google Books volume id) the upload
     * form's metadata lookup actually supports -- Open Library never
     * accepts a bare work id there, and staff refresh needs a real id to
     * re-fetch from -- never an internal id that can't be looked up again.
     *
     * @param array<string, mixed>|null $raw
     * @param array<string, mixed>      $identifiers
     *
     * @return array{source: string, sourceId: string, identityKey: string}|null
     */
    private function bookIdentity(?array $raw, array $identifiers): ?array
    {
        $workKey = $raw['works'][0]['key'] ?? null;
        $workId = \is_string($workKey) ? basename($workKey) : null;

        if (\is_string($workId) && $workId !== '') {
            $sourceId = $this->bookEditionSourceId($raw, $identifiers);
            $source = \is_string($identifiers['source'] ?? null) && $identifiers['source'] !== '' ? $identifiers['source'] : 'open-library';

            return [
                'source'      => $source,
                'sourceId'    => $sourceId ?? $workId,
                'identityKey' => "book:open-library-work:{$workId}",
            ];
        }

        $googleVolumeId = \is_string($raw['google_books_item']['id'] ?? null) ? $raw['google_books_item']['id'] : null;

        if ($googleVolumeId !== null) {
            $isbn = $this->googleBooksIsbn($raw) ?? $this->bookEditionSourceId($raw, $identifiers);

            return [
                'source'      => 'google-books',
                'sourceId'    => $isbn ?? $googleVolumeId,
                'identityKey' => "book:google-books:{$googleVolumeId}",
            ];
        }

        $sourceId = $this->bookEditionSourceId($raw, $identifiers);

        if ($sourceId === null) {
            return null;
        }

        $source = \is_string($identifiers['source'] ?? null) && $identifiers['source'] !== '' ? $identifiers['source'] : 'open-library';
        $normalized = strtolower(str_replace([' ', '-'], '', trim($sourceId)));

        return ['source' => $source, 'sourceId' => $sourceId, 'identityKey' => "book:{$source}:{$normalized}"];
    }

    /**
     * @param array<string, mixed>|null $raw
     * @param array<string, mixed>      $identifiers
     */
    private function bookEditionSourceId(?array $raw, array $identifiers): ?string
    {
        $sourceId = $identifiers['open_library_edition_id'] ?? null;

        if (\is_string($sourceId) && $sourceId !== '') {
            return $sourceId;
        }

        return \is_string($raw['key'] ?? null) ? basename($raw['key']) : null;
    }

    /**
     * The ISBN-13 (preferred) or ISBN-10 a Google Books volume payload
     * advertises, so the Work's usable source id is always something the
     * book lookup endpoint actually accepts, never the internal volume id.
     *
     * @param array<string, mixed>|null $raw
     */
    private function googleBooksIsbn(?array $raw): ?string
    {
        $identifiers = $raw['industryIdentifiers'] ?? [];

        if (!\is_array($identifiers)) {
            return null;
        }

        $isbn10 = null;

        foreach ($identifiers as $entry) {
            if (!\is_array($entry) || !\is_string($entry['identifier'] ?? null)) {
                continue;
            }

            if (($entry['type'] ?? null) === 'ISBN_13') {
                return $entry['identifier'];
            }

            if (($entry['type'] ?? null) === 'ISBN_10') {
                $isbn10 = $entry['identifier'];
            }
        }

        return $isbn10;
    }

    /**
     * @param Lookup $lookup
     *
     * @return array<string, mixed>
     */
    private function identifiersFromLookup(string $kind, array $lookup): array
    {
        $identifiers = $lookup['identifiers'] ?? [];

        if ($kind === 'book') {
            $identifiers['source'] = match ($lookup['source'] ?? null) {
                'Open Library' => 'open-library',
                'Google Books' => 'google-books',
                default        => null,
            };
        }

        return $identifiers;
    }

    /**
     * @param Lookup $lookup
     *
     * @return array{source: string, sourceId: string, identityKey: string}|null
     */
    private function canonicalIdentityFromLookup(string $kind, array $lookup): ?array
    {
        return $this->canonicalIdentity($kind, $lookup['raw'] ?? null, $this->identifiersFromLookup($kind, $lookup));
    }

    /**
     * @return array<string, mixed>
     */
    private function storedBookIdentifiers(Torrent $torrent): array
    {
        $metadata = $this->storedMetadata($torrent);

        if ($metadata === null || !\in_array($metadata->source, ['open-library', 'google-books'], true)) {
            return [];
        }

        return ['source' => $metadata->source, 'open_library_edition_id' => $metadata->source_id];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function storedRawFor(string $kind, Torrent $torrent): ?array
    {
        if (!\in_array($kind, ['music', 'book'], true)) {
            return null;
        }

        $expectedSource = $kind === 'music' ? 'musicbrainz' : null;
        $metadata = $this->storedMetadata($torrent);

        if ($metadata === null) {
            return null;
        }

        if ($expectedSource !== null && $metadata->source !== $expectedSource) {
            return null;
        }

        if ($kind === 'book' && !\in_array($metadata->source, ['open-library', 'google-books'], true)) {
            return null;
        }

        return $metadata->raw;
    }

    private function storedMetadata(Torrent $torrent): ?TorrentMetadata
    {
        if (!$torrent->exists) {
            return null;
        }

        if ($torrent->relationLoaded('metadata')) {
            return $torrent->getRelation('metadata');
        }

        return TorrentMetadata::query()->where('torrent_id', '=', $torrent->getKey())->first();
    }

    /**
     * Reconstructs a Lookup-shaped array purely from already-persisted
     * provider rows/TorrentMetadata for {@see sync()}, never calling out to
     * a provider. Returns null when there is nothing stored yet to enrich
     * the Work with (e.g. brand new provisional content).
     *
     * @return Lookup|null
     */
    private function resolveLookupFromStorage(array $identity, Torrent $torrent): ?array
    {
        return match ($identity['kind']) {
            'movie' => $this->movieLookupFromStorage($identity),
            'tv'    => $this->tvLookupFromStorage($identity),
            'game'  => $this->gameLookupFromStorage($identity),
            'music', 'book' => $this->metadataLookupFromStorage($torrent),
            default => null,
        };
    }

    /**
     * @return Lookup|null
     */
    private function movieLookupFromStorage(array $identity): ?array
    {
        $movie = TmdbMovie::query()->find($identity['sourceId']);

        if ($movie === null) {
            return null;
        }

        return [
            'title'       => $movie->title,
            'description' => (string) $movie->overview,
            'source'      => 'TMDB',
            'source_url'  => "https://www.themoviedb.org/movie/{$movie->id}",
            'cover_url'   => $movie->poster,
            ...(filled($movie->raw) ? ['raw' => $movie->raw] : []),
        ];
    }

    /**
     * @return Lookup|null
     */
    private function tvLookupFromStorage(array $identity): ?array
    {
        $tv = TmdbTv::query()->find($identity['sourceId']);

        if ($tv === null) {
            return null;
        }

        return [
            'title'       => $tv->name,
            'description' => (string) $tv->overview,
            'source'      => 'TMDB',
            'source_url'  => "https://www.themoviedb.org/tv/{$tv->id}",
            'cover_url'   => $tv->poster,
            ...(filled($tv->raw) ? ['raw' => $tv->raw] : []),
        ];
    }

    /**
     * @return Lookup|null
     */
    private function gameLookupFromStorage(array $identity): ?array
    {
        $game = IgdbGame::query()->find($identity['sourceId']);

        if ($game === null) {
            return null;
        }

        return [
            'title'       => $game->name,
            'description' => (string) $game->summary,
            'source'      => 'IGDB',
            'source_url'  => $game->url,
            'cover_url'   => $game->cover_image_id !== null ? "https://images.igdb.com/igdb/image/upload/t_cover_big/{$game->cover_image_id}.jpg" : null,
            ...(filled($game->raw) ? ['raw' => $game->raw] : []),
        ];
    }

    /**
     * @return Lookup|null
     */
    private function metadataLookupFromStorage(Torrent $torrent): ?array
    {
        $metadata = $this->storedMetadata($torrent);

        if ($metadata === null) {
            return null;
        }

        $raw = \is_array($metadata->raw) ? $metadata->raw : [];

        $lookup = [
            'title'       => $metadata->title,
            'description' => (string) $metadata->summary,
            'source'      => match ($metadata->source) {
                'musicbrainz'   => 'MusicBrainz',
                'open-library'  => 'Open Library',
                'google-books'  => 'Google Books',
                default         => $metadata->source,
            },
            'source_url' => $metadata->source_url,
            'raw'        => $raw,
        ];

        // Only ever set cover_url when a real cover can be reconstructed;
        // omitting the key (rather than sending null) means apply() leaves
        // an already-set Work cover untouched instead of clearing it every
        // time a later edition's job happens to lack the same artwork.
        $coverUrl = match ($metadata->source) {
            'musicbrainz'  => isset($raw['id']) ? \sprintf('https://coverartarchive.org/%s/%s/front', isset($raw['release-group']) ? 'release' : 'release-group', $raw['id']) : null,
            'open-library' => \is_string($raw['key'] ?? null) ? \sprintf('https://covers.openlibrary.org/b/olid/%s-L.jpg', basename($raw['key'])) : null,
            'google-books' => \is_string($raw['imageLinks']['thumbnail'] ?? null)
                ? preg_replace('/^http:/', 'https:', $raw['imageLinks']['thumbnail'])
                : (\is_string($raw['imageLinks']['smallThumbnail'] ?? null) ? preg_replace('/^http:/', 'https:', $raw['imageLinks']['smallThumbnail']) : null),
            default => null,
        };

        if ($coverUrl !== null) {
            $lookup['cover_url'] = $coverUrl;
        }

        return $lookup;
    }

    /**
     * @param array{kind: string, source: ?string, sourceId: ?string, identityKey: string, provisional: bool} $identity
     */
    private function findOrCreateWork(array $identity, ?string $fallbackTitle = null): MediaWork
    {
        $existing = MediaWork::query()->where('identity_key', '=', $identity['identityKey'])->first();

        if ($existing !== null) {
            return $existing;
        }

        try {
            return DB::transaction(fn () => MediaWork::query()->create([
                'identity_key' => $identity['identityKey'],
                'kind'         => $identity['kind'],
                'source'       => $identity['source'],
                'source_id'    => $identity['sourceId'],
                'title'        => $fallbackTitle !== null && $fallbackTitle !== '' ? $fallbackTitle : $identity['identityKey'],
            ]));
        } catch (UniqueConstraintViolationException) {
            // Lost a race to create the same identity concurrently; the
            // unique index on identity_key is the source of truth.
            return MediaWork::query()->where('identity_key', '=', $identity['identityKey'])->firstOrFail();
        }
    }

    /**
     * Normalized, category-specific facts computed from a raw provider
     * payload, used both for the Work's own common/shared snapshot and
     * (music/book) for the per-edition facets a metadata job stores on
     * {@see \App\Models\TorrentMetadata} so category filters can match any
     * accessible edition's actual format/language/publisher, not just
     * whichever edition's job last ran.
     *
     * @param array<string, mixed> $raw
     *
     * @return array<string, mixed>|null
     */
    public function computeFacets(string $kind, array $raw): ?array
    {
        return match ($kind) {
            'music' => $this->musicFacets($raw),
            'game'  => $this->gameFacets($raw),
            'book'  => $this->bookFacets($raw),
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array{artists: list<string>, labels: list<string>, formats: list<string>, year: ?int}
     */
    private function musicFacets(array $raw): array
    {
        $artists = collect($raw['artist-credit'] ?? [])
            ->map(fn (mixed $credit): ?string => \is_array($credit) && \is_string($credit['name'] ?? null) ? $credit['name'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $labels = collect($raw['label-info'] ?? [])
            ->map(fn (mixed $info): ?string => \is_array($info) && \is_string($info['label']['name'] ?? null) ? $info['label']['name'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $formats = collect($raw['media'] ?? [])
            ->map(fn (mixed $medium): ?string => \is_array($medium) && \is_string($medium['format'] ?? null) ? $medium['format'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $date = $raw['date'] ?? $raw['first-release-date'] ?? null;
        $year = \is_string($date) && preg_match('/^(\d{4})/', $date, $matches) === 1 ? (int) $matches[1] : null;

        return ['artists' => $artists, 'labels' => $labels, 'formats' => $formats, 'year' => $year];
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return array{platforms: list<string>, genres: list<string>, developers: list<string>}
     */
    private function gameFacets(array $raw): array
    {
        $platforms = collect($raw['platforms'] ?? [])
            ->map(fn (mixed $platform): ?string => \is_array($platform) && \is_string($platform['name'] ?? null) ? $platform['name'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $genres = collect($raw['genres'] ?? [])
            ->map(fn (mixed $genre): ?string => \is_array($genre) && \is_string($genre['name'] ?? null) ? $genre['name'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $developers = collect($raw['involved_companies'] ?? [])
            ->filter(fn (mixed $company): bool => \is_array($company) && ($company['developer'] ?? false) === true)
            ->map(fn (array $company): ?string => \is_string($company['company']['name'] ?? null) ? $company['company']['name'] : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return ['platforms' => $platforms, 'genres' => $genres, 'developers' => $developers];
    }

    /**
     * Handles both Open Library's edition shape (`author_names`,
     * `languages[].key`, `publishers[]`) and Google Books' `volumeInfo`
     * shape (`authors[]`, singular `language`/`publisher`), since either can
     * be the trusted raw payload for a book edition.
     *
     * @param array<string, mixed> $raw
     *
     * @return array{authors: list<string>, languages: list<string>, publishers: list<string>}
     */
    private function bookFacets(array $raw): array
    {
        $authors = collect($raw['author_names'] ?? $raw['authors'] ?? [])
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->unique()
            ->values()
            ->all();

        $languages = collect($raw['languages'] ?? [])
            ->map(fn (mixed $language): ?string => \is_array($language) && \is_string($language['key'] ?? null) ? basename($language['key']) : null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($languages === [] && \is_string($raw['language'] ?? null) && $raw['language'] !== '') {
            $languages = [$raw['language']];
        }

        $publishers = collect($raw['publishers'] ?? [])
            ->map(fn (mixed $publisher): ?string => \is_string($publisher) ? $publisher : (\is_array($publisher) && \is_string($publisher['name'] ?? null) ? $publisher['name'] : null))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($publishers === [] && \is_string($raw['publisher'] ?? null) && $raw['publisher'] !== '') {
            $publishers = [$raw['publisher']];
        }

        return ['authors' => $authors, 'languages' => $languages, 'publishers' => $publishers];
    }
}
