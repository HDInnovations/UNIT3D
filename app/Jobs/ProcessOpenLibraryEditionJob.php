<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\GlobalRateLimit;
use App\Models\TorrentMetadata;
use App\Services\OpenLibrary\OpenLibraryClient;
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

    public function handle(OpenLibraryClient $openLibrary): void
    {
        $book = $openLibrary->edition($this->identifier);
        $olid = basename($book['key']);
        $authors = collect($book['author_names'] ?? [])
            ->filter(fn (mixed $name): bool => \is_string($name) && $name !== '')
            ->join(', ');
        $publishers = collect($book['publishers'] ?? [])
            ->map(fn (mixed $publisher): ?string => \is_string($publisher) ? $publisher : (\is_array($publisher) && isset($publisher['name']) && \is_string($publisher['name']) ? $publisher['name'] : null))
            ->filter()
            ->join(', ');

        TorrentMetadata::query()->updateOrCreate(['torrent_id' => $this->torrentId], [
            'source'      => 'open-library',
            'source_id'   => $olid,
            'title'       => $book['title'],
            'subtitle'    => $authors === '' ? null : $authors,
            'released_on' => $book['publish_date'] ?? null,
            'publisher'   => $publishers === '' ? null : $publishers,
            'item_count'  => isset($book['number_of_pages']) && is_numeric($book['number_of_pages']) ? (int) $book['number_of_pages'] : null,
            'summary'     => isset($book['notes']) && \is_string($book['notes']) ? $book['notes'] : null,
            'source_url'  => "https://openlibrary.org/books/{$olid}",
            'raw'         => $book,
        ]);
    }
}
