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

namespace App\Services\GoogleBooks;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin, bounded client around the public (keyless-capable) Google Books
 * volumes endpoint. Used only to look up a single volume by its exact ISBN,
 * never a fuzzy title search, so the result can be trusted to describe the
 * requested edition.
 */
final class GoogleBooksClient
{
    /**
     * Find the Google Books volume whose industryIdentifiers contains an
     * exact match for the given (already-normalized, digits/X only) ISBN.
     *
     * @return null|array<string, mixed> the matching item's `volumeInfo`, or null when no exact match exists
     */
    public function findByIsbn(string $isbn): ?array
    {
        $query = [
            'q'          => "isbn:{$isbn}",
            'country'    => 'US',
            'maxResults' => 5,
        ];

        $key = config('api-keys.google-books');

        if (\is_string($key) && $key !== '') {
            $query['key'] = $key;
        }

        $response = Http::acceptJson()
            ->timeout(5)
            ->connectTimeout(3)
            ->get('https://www.googleapis.com/books/v1/volumes', $query)
            ->throw();

        $items = $response->json('items');

        if (!\is_array($items)) {
            return null;
        }

        foreach ($items as $item) {
            if (!\is_array($item) || !isset($item['volumeInfo']) || !\is_array($item['volumeInfo'])) {
                continue;
            }

            $identifiers = $item['volumeInfo']['industryIdentifiers'] ?? [];

            if (!\is_array($identifiers)) {
                continue;
            }

            foreach ($identifiers as $identifier) {
                if (!\is_array($identifier) || !isset($identifier['identifier']) || !\is_string($identifier['identifier'])) {
                    continue;
                }

                $normalized = strtoupper(preg_replace('/[^0-9X]/i', '', $identifier['identifier']) ?? '');

                if ($normalized === $isbn) {
                    $volumeInfo = $item['volumeInfo'];
                    $itemLevel = array_diff_key($item, ['volumeInfo' => true]);

                    if ($itemLevel !== []) {
                        $volumeInfo['google_books_item'] = $itemLevel;
                    }

                    return $volumeInfo;
                }
            }
        }

        return null;
    }
}
