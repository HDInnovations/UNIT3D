<?php

declare(strict_types=1);

namespace App\Services\OpenLibrary;

use App\Services\Metadata\ProviderQueryEscaper;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class OpenLibraryClient
{
    /**
     * Bounded title search. Each returned document is a *work*, but only
     * ones carrying at least one concrete edition id are useful here: the
     * existing lookup only understands editions/ISBNs, never bare Open
     * Library work ids, so results without an edition are filtered out by
     * the caller rather than handed back as a false choice.
     *
     * @return list<array<string, mixed>>
     */
    public function search(string $query, int $limit = 12): array
    {
        $limit = max(1, min($limit, 12));

        $data = Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->connectTimeout(3)
            ->timeout(8)
            ->get('https://openlibrary.org/search.json', [
                'q'      => ProviderQueryEscaper::lucene($query),
                'fields' => 'key,title,author_name,first_publish_year,cover_i,edition_key',
                'limit'  => $limit,
            ])
            ->throw()
            ->json();

        if (!\is_array($data) || !isset($data['docs']) || !\is_array($data['docs'])) {
            throw new RuntimeException('Open Library search returned an unexpected response.');
        }

        return $data['docs'];
    }

    private function userAgent(): string
    {
        return \sprintf('%s metadata lookup (%s)', config('app.name'), config('app.url'));
    }

    /**
     * @return array<string, mixed>
     */
    public function edition(string $identifier): array
    {
        $identifier = strtoupper(trim($identifier));
        $edition = preg_match('/^OL\d+M$/', $identifier) === 1
            ? $identifier
            : preg_replace('/[^0-9X]/', '', $identifier);
        $path = str_starts_with($edition, 'OL') ? "/books/{$edition}.json" : "/isbn/{$edition}.json";

        $book = Http::acceptJson()
            ->withUserAgent($this->userAgent())
            ->get("https://openlibrary.org{$path}")
            ->throw()
            ->json();

        if (!\is_array($book) || !isset($book['title'], $book['key'])) {
            throw new RuntimeException("Open Library edition {$identifier} was not found.");
        }

        $authorDetails = collect($book['authors'] ?? [])
            ->map(function (mixed $author): ?array {
                if (!\is_array($author) || !isset($author['key']) || !\is_string($author['key'])) {
                    return null;
                }

                $authorData = Http::acceptJson()
                    ->withUserAgent($this->userAgent())
                    ->get('https://openlibrary.org'.$author['key'].'.json')
                    ->throw()
                    ->json();

                return \is_array($authorData) ? $authorData : null;
            })
            ->filter()
            ->values();

        $book['author_details'] = $authorDetails->all();

        $book['author_names'] = $authorDetails
            ->map(fn (array $authorData): ?string => isset($authorData['name']) && \is_string($authorData['name']) ? $authorData['name'] : null)
            ->filter()
            ->values()
            ->all();

        $workDetails = collect($book['works'] ?? [])
            ->map(function (mixed $work): ?array {
                if (!\is_array($work) || !isset($work['key']) || !\is_string($work['key'])) {
                    return null;
                }

                $workData = Http::acceptJson()
                    ->withUserAgent($this->userAgent())
                    ->get('https://openlibrary.org'.$work['key'].'.json')
                    ->throw()
                    ->json();

                return \is_array($workData) ? $workData : null;
            })
            ->filter()
            ->values()
            ->all();

        $book['work_details'] = $workDetails;

        return $book;
    }
}
