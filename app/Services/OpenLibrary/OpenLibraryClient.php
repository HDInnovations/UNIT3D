<?php

declare(strict_types=1);

namespace App\Services\OpenLibrary;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class OpenLibraryClient
{
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
            ->withUserAgent(\sprintf('%s metadata lookup (%s)', config('app.name'), config('app.url')))
            ->get("https://openlibrary.org{$path}")
            ->throw()
            ->json();

        if (!\is_array($book) || !isset($book['title'], $book['key'])) {
            throw new RuntimeException("Open Library edition {$identifier} was not found.");
        }

        $book['author_names'] = collect($book['authors'] ?? [])
            ->map(function (mixed $author): ?string {
                if (!\is_array($author) || !isset($author['key']) || !\is_string($author['key'])) {
                    return null;
                }

                $authorData = Http::acceptJson()
                    ->withUserAgent(\sprintf('%s metadata lookup (%s)', config('app.name'), config('app.url')))
                    ->get('https://openlibrary.org'.$author['key'].'.json')
                    ->throw()
                    ->json();

                return \is_array($authorData) && isset($authorData['name']) && \is_string($authorData['name']) ? $authorData['name'] : null;
            })
            ->filter()
            ->values()
            ->all();

        return $book;
    }
}
