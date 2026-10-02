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

use App\Models\MediaWork;
use App\Models\User;
use App\Services\Staff\MetadataQualityPreviewStore;
use Illuminate\Validation\ValidationException;

function metadataQualityWork(array $overrides = []): MediaWork
{
    return MediaWork::create(array_merge([
        'identity_key' => 'movie:tmdb:'.fake()->unique()->randomNumber(8),
        'kind'         => 'movie',
        'source'       => 'TMDB',
        'source_id'    => (string) fake()->unique()->randomNumber(6),
        'title'        => 'Original Title',
        'description'  => 'Original description',
        'cover_url'    => 'https://example.test/old-cover.jpg',
        'raw'          => ['old' => true],
    ], $overrides));
}

/**
 * @return array<string, mixed>
 */
function sampleLookup(array $overrides = []): array
{
    return array_merge([
        'title'                => 'New Title',
        'description'          => 'New description',
        'description_language' => 'en',
        'source'               => 'TMDB',
        'source_url'           => null,
        'cover_url'            => 'https://example.test/new-cover.jpg',
        'identifiers'          => [],
        'warning'              => null,
        'raw'                  => ['new' => true],
        'details'              => [],
    ], $overrides);
}

test('a preview token is redeemed once by its own user for its own work', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $user = User::factory()->create();
    $work = metadataQualityWork();

    $token = $store->remember($user, $work, sampleLookup());
    $result = $store->retrieve($user, $work, $token);

    expect($result['lookup']['title'])->toBe('New Title');
});

test('a preview token cannot be redeemed twice', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $user = User::factory()->create();
    $work = metadataQualityWork();

    $token = $store->remember($user, $work, sampleLookup());
    $store->retrieve($user, $work, $token);

    expect(fn () => $store->retrieve($user, $work, $token))
        ->toThrow(ValidationException::class);
});

test('a preview token is rejected when redeemed by a different user', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $work = metadataQualityWork();

    $token = $store->remember($owner, $work, sampleLookup());

    expect(fn () => $store->retrieve($other, $work, $token))
        ->toThrow(ValidationException::class);

    // The mismatched attempt must not have burned the rightful owner's token.
    $result = $store->retrieve($owner, $work, $token);
    expect($result['lookup']['title'])->toBe('New Title');
});

test('a preview token is rejected when redeemed against a different work', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $user = User::factory()->create();
    $work = metadataQualityWork();
    $otherWork = metadataQualityWork();

    $token = $store->remember($user, $work, sampleLookup());

    expect(fn () => $store->retrieve($user, $otherWork, $token))
        ->toThrow(ValidationException::class);
});

test('a preview token is rejected once the work changed since the preview was taken', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $user = User::factory()->create();
    $work = metadataQualityWork();

    $token = $store->remember($user, $work, sampleLookup());

    // Another reviewer (or another request) already changed the Work.
    $work->update(['description' => 'Someone else already changed this']);

    expect(fn () => $store->retrieve($user, $work, $token))
        ->toThrow(ValidationException::class);
});

test('an unknown or expired token is rejected', function (): void {
    $store = app(MetadataQualityPreviewStore::class);
    $user = User::factory()->create();
    $work = metadataQualityWork();

    expect(fn () => $store->retrieve($user, $work, 'not-a-real-token'))
        ->toThrow(ValidationException::class);
});
