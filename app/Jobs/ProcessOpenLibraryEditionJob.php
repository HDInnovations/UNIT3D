<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\Scopes\ApprovedScope;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use App\Services\Media\MediaWorkCatalog;
use App\Services\Metadata\TorrentMetadataLookup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessOpenLibraryEditionJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $torrentId, public string $identifier)
    {
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping("open-library-edition:{$this->identifier}")->dontRelease()->expireAfter(30),
            new RateLimited(GlobalRateLimit::OPEN_LIBRARY),
        ];
    }

    public function handle(TorrentMetadataLookup $lookup): void
    {
        $torrent = Torrent::query()
            ->withoutGlobalScope(ApprovedScope::class)
            ->with('category')
            ->findOrFail($this->torrentId);

        if ($torrent->category === null) {
            return;
        }

        $result = $lookup->lookup($torrent->category, $this->identifier);

        $raw = $result['raw'] ?? null;
        $source = $result['source'] ?? null;

        if (!\is_array($raw)) {
            return;
        }

        $attributes = match ($source) {
            'Google Books'  => $this->attributesFromGoogleBooks($raw, $result),
            'Open Library'  => $this->attributesFromOpenLibrary($raw, $result),
            default         => null,
        };

        if ($attributes === null) {
            return;
        }

        $catalog = app(MediaWorkCatalog::class);

        // This edition's own facets (language/publisher legitimately differ
        // between translations/reprints of the same book); category filters
        // match on this, not the Work's shared last-synced snapshot, so
        // every accessible edition stays findable.
        $attributes['facets'] = $catalog->computeFacets('book', $raw);

        TorrentMetadata::query()->updateOrCreate(['torrent_id' => $this->torrentId], $attributes);

        $catalog->sync($torrent);

        Torrent::query()->whereKey($torrent->id)->searchable();
    }

    /**
     * @param array<string, mixed> $raw the full Open Library edition payload
     * @param array<string, mixed> $result the lookup() LookupResult
     *
     * @return array<string, mixed>
     */
    private function attributesFromOpenLibrary(array $raw, array $result): array
    {
        $olid = \is_string($raw['key'] ?? null) ? basename($raw['key']) : $this->identifier;

        $authors = collect($raw['author_names'] ?? [])
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');

        $publishers = collect($raw['publishers'] ?? [])
            ->map(fn (mixed $publisher): ?string => \is_string($publisher) ? $publisher : (\is_array($publisher) && \is_string($publisher['name'] ?? null) ? $publisher['name'] : null))
            ->filter()
            ->join(', ');

        $description = $raw['description'] ?? null;
        $summary = \is_string($description)
            ? $description
            : (\is_array($description) && \is_string($description['value'] ?? null) ? $description['value'] : null);

        return [
            'source'      => 'open-library',
            'source_id'   => $olid,
            'title'       => $raw['title'] ?? $result['title'],
            'subtitle'    => $authors === '' ? null : $authors,
            'released_on' => $raw['publish_date'] ?? null,
            'publisher'   => $publishers === '' ? null : $publishers,
            'item_count'  => isset($raw['number_of_pages']) && is_numeric($raw['number_of_pages']) ? (int) $raw['number_of_pages'] : null,
            'summary'     => $summary,
            'source_url'  => "https://openlibrary.org/books/{$olid}",
            'raw'         => $raw,
        ];
    }

    /**
     * @param array<string, mixed> $raw the Google Books volumeInfo payload (with item-level fields under `google_books_item`)
     * @param array<string, mixed> $result the lookup() LookupResult
     *
     * @return array<string, mixed>
     */
    private function attributesFromGoogleBooks(array $raw, array $result): array
    {
        $sourceId = \is_string($raw['google_books_item']['id'] ?? null) ? $raw['google_books_item']['id'] : $this->identifier;

        $authors = collect($raw['authors'] ?? [])
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');

        $sourceUrl = \is_string($raw['previewLink'] ?? null)
            ? $raw['previewLink']
            : (\is_string($raw['infoLink'] ?? null) ? $raw['infoLink'] : "https://books.google.com/books?id={$sourceId}");

        return [
            'source'      => 'google-books',
            'source_id'   => $sourceId,
            'title'       => $raw['title'] ?? $result['title'],
            'subtitle'    => $authors === '' ? null : $authors,
            'released_on' => $raw['publishedDate'] ?? null,
            'publisher'   => \is_string($raw['publisher'] ?? null) ? $raw['publisher'] : null,
            'item_count'  => isset($raw['pageCount']) && is_numeric($raw['pageCount']) ? (int) $raw['pageCount'] : null,
            'summary'     => \is_string($raw['description'] ?? null) ? strip_tags($raw['description']) : null,
            'source_url'  => $sourceUrl,
            'raw'         => $raw,
        ];
    }
}
