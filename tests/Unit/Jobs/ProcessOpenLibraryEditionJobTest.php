<?php

declare(strict_types=1);

use App\Jobs\ProcessOpenLibraryEditionJob;
use App\Models\Category;
use App\Models\Torrent;
use App\Models\TorrentMetadata;
use App\Services\Metadata\TorrentMetadataLookup;
use Illuminate\Support\Facades\Http;

// A well-known, checksum-valid ISBN-13 (978-0-306-40615-7).
const JOB_TEST_VALID_ISBN_13 = '9780306406157';

function bookCategoryForJobTest(): Category
{
    return Category::factory()->create([
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'book_meta'  => true,
        'no_meta'    => false,
    ]);
}

it('persists the Google Books raw payload when only Google has a verified Czech match', function (): void {
    $torrent = Torrent::factory()->create(['category_id' => bookCategoryForJobTest()->id]);

    Http::fake([
        'https://www.googleapis.com/books/v1/volumes*' => Http::response([
            'items' => [[
                'id' => 'google-volume-id',
                'volumeInfo' => [
                    'title'       => 'Ceska Kniha',
                    'authors'     => ['Author One', 'Author Two'],
                    'publisher'   => 'Google Publisher',
                    'publishedDate' => '2001-05-01',
                    'pageCount'   => 321,
                    'description' => 'Popis v cestine.',
                    'language'    => 'cs',
                    'previewLink' => 'https://books.google.com/preview',
                    'industryIdentifiers' => [
                        ['type' => 'ISBN_13', 'identifier' => JOB_TEST_VALID_ISBN_13],
                    ],
                ],
            ]],
        ], 200),
    ]);

    (new ProcessOpenLibraryEditionJob($torrent->id, JOB_TEST_VALID_ISBN_13))->handle(app(TorrentMetadataLookup::class));

    // Google Books is authoritative here, so Open Library must never be contacted.
    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'openlibrary.org'));

    $metadata = TorrentMetadata::query()->where('torrent_id', $torrent->id)->firstOrFail();

    expect($metadata->source)->toBe('google-books')
        ->and($metadata->source_id)->toBe('google-volume-id')
        ->and($metadata->title)->toBe('Ceska Kniha')
        ->and($metadata->subtitle)->toBe('Author One, Author Two')
        ->and($metadata->publisher)->toBe('Google Publisher')
        ->and($metadata->released_on)->toBe('2001-05-01')
        ->and($metadata->item_count)->toBe(321)
        ->and($metadata->summary)->toBe('Popis v cestine.')
        ->and($metadata->source_url)->toBe('https://books.google.com/preview')
        ->and($metadata->raw['title'])->toBe('Ceska Kniha')
        ->and($metadata->raw['google_books_item']['id'])->toBe('google-volume-id');
});
