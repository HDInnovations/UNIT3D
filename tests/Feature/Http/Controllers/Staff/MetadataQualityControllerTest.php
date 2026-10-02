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

use App\Models\Category;
use App\Models\Group;
use App\Models\MediaWork;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Http;

function metadataQualityMusicCategory(): Category
{
    return Category::factory()->create([
        'movie_meta' => false, 'tv_meta' => false, 'game_meta' => false,
        'music_meta' => true, 'book_meta' => false, 'no_meta' => false,
    ]);
}

function metadataQualityMovieCategory(): Category
{
    return Category::factory()->create([
        'movie_meta' => true, 'tv_meta' => false, 'game_meta' => false,
        'music_meta' => false, 'book_meta' => false, 'no_meta' => false,
    ]);
}

test('a user without modo, editor, or torrent-modo privileges is forbidden on every endpoint', function (): void {
    $plain = User::factory()->create(['group_id' => Group::factory()->create()->id]);
    $work = MediaWork::create([
        'identity_key' => 'movie:tmdb:9001',
        'kind'         => 'movie',
        'source'       => 'TMDB',
        'source_id'    => '9001',
        'title'        => 'A Movie',
    ]);

    $this->actingAs($plain)
        ->get(route('staff.metadata-quality.index'))
        ->assertForbidden();

    $this->actingAs($plain)
        ->post(route('staff.metadata-quality.preview', ['work' => $work]))
        ->assertForbidden();

    $this->actingAs($plain)
        ->patch(route('staff.metadata-quality.update', ['work' => $work]), [
            'preview_token' => 'irrelevant',
            'fields'        => ['title'],
        ])
        ->assertForbidden();
});

test('an editor without modo privileges is authorized to view the dashboard', function (): void {
    $editor = User::factory()->create([
        'group_id' => Group::factory()->create(['is_editor' => true])->id,
    ]);

    $this->actingAs($editor)
        ->get(route('staff.metadata-quality.index'))
        ->assertOk();
});

test('a torrent-modo without modo or editor privileges is authorized to view the dashboard', function (): void {
    $torrentModo = User::factory()->create([
        'group_id' => Group::factory()->create(['is_torrent_modo' => true])->id,
    ]);

    $this->actingAs($torrentModo)
        ->get(route('staff.metadata-quality.index'))
        ->assertOk();
});

test('missing source filter includes null and empty records but excludes complete records', function (): void {
    $editor = User::factory()->create(['group_id' => Group::factory()->create(['is_editor' => true])->id]);
    $id = 9000;
    foreach (['Absent source' => null, 'Empty source' => [], 'Complete source' => ['id' => 9003]] as $title => $raw) {
        ++$id;
        MediaWork::create(['identity_key' => 'movie:tmdb:'.$id, 'kind' => 'movie', 'source' => 'tmdb', 'source_id' => (string) $id, 'title' => $title, 'raw' => $raw]);
    }
    $this->actingAs($editor)->get(route('staff.metadata-quality.index', ['status' => 'missing_raw']))
        ->assertOk()->assertSee('Absent source')->assertSee('Empty source')->assertDontSee('Complete source');
});

test('an empty music description from the provider is reported as expected, not as a quality error', function (): void {
    Http::fake([
        'https://musicbrainz.org/ws/2/release/*' => Http::response([
            'id'            => '11111111-1111-1111-1111-111111111111',
            'title'         => 'Some Album',
            'artist-credit' => [['name' => 'Some Artist']],
        ], 200),
    ]);

    $category = metadataQualityMusicCategory();
    $work = MediaWork::create([
        'identity_key' => 'music:musicbrainz:11111111-1111-1111-1111-111111111111',
        'kind'         => 'music',
        'source'       => 'MusicBrainz',
        'source_id'    => '11111111-1111-1111-1111-111111111111',
        'title'        => 'Old Title',
        'description'  => null,
        'cover_url'    => 'https://example.test/old-cover.jpg',
        'raw'          => ['old' => true],
    ]);
    Torrent::factory()->create(['category_id' => $category->id, 'media_work_id' => $work->id]);

    $response = $this->post(route('staff.metadata-quality.preview', ['work' => $work]));

    $response->assertOk();
    $response->assertViewHas('diff', fn (array $diff): bool => $diff['description']['expected_empty'] === true);

    $work->refresh();
    // A legitimately empty optional field is never written as a metadata_error,
    // and nothing that already existed on the Work was touched.
    expect($work->metadata_error)->toBeNull()
        ->and($work->cover_url)->toBe('https://example.test/old-cover.jpg')
        ->and($work->raw)->toBe(['old' => true]);
});

test('a provider outage during preview is recorded distinctly from a not-found and never erases existing metadata', function (): void {
    Http::fake([
        'https://musicbrainz.org/ws/2/*' => Http::sequence()
            ->push([], 503)
            ->push(['error' => 'Not Found'], 404)
            ->push(['error' => 'Not Found'], 404),
    ]);

    $category = metadataQualityMusicCategory();
    $work = MediaWork::create([
        'identity_key' => 'music:musicbrainz:22222222-2222-2222-2222-222222222222',
        'kind'         => 'music',
        'source'       => 'MusicBrainz',
        'source_id'    => '22222222-2222-2222-2222-222222222222',
        'title'        => 'Kept Title',
        'description'  => 'Kept description',
        'cover_url'    => 'https://example.test/kept-cover.jpg',
        'raw'          => ['kept' => true],
    ]);
    Torrent::factory()->create(['category_id' => $category->id, 'media_work_id' => $work->id]);

    $outage = $this->post(route('staff.metadata-quality.preview', ['work' => $work]));
    $outage->assertSessionHasErrors('metadata_quality');

    $work->refresh();
    expect($work->metadata_error)->toBe(__('metadata-quality.errors.source-unavailable'))
        ->and($work->title)->toBe('Kept Title')
        ->and($work->description)->toBe('Kept description')
        ->and($work->cover_url)->toBe('https://example.test/kept-cover.jpg')
        ->and($work->raw)->toBe(['kept' => true]);

    $notFound = $this->post(route('staff.metadata-quality.preview', ['work' => $work]));
    $notFound->assertSessionHasErrors('metadata_quality');

    $work->refresh();
    expect($work->metadata_error)->toBe(__('metadata-quality.errors.source-not-found'))
        ->and($work->metadata_error)->not->toBe(__('metadata-quality.errors.source-unavailable'))
        ->and($work->title)->toBe('Kept Title');
});

test('a reviewed refresh changes only selected fields and cannot be replayed', function (): void {
    config(['api-keys.tmdb' => 'test-key']);
    Http::fake([
        'https://api.themoviedb.org/3/movie/*' => Http::response([
            'id' => 111, 'title' => 'Fetched Title', 'overview' => 'Fetched overview',
        ], 200),
    ]);
    $category = metadataQualityMovieCategory();
    $work = MediaWork::create([
        'identity_key' => 'movie:tmdb:111', 'kind' => 'movie',
        'source' => 'tmdb', 'source_id' => '111', 'title' => 'Original Title',
        'description' => 'Retained description', 'raw' => ['retained' => true],
    ]);
    Torrent::factory()->create(['category_id' => $category->id, 'media_work_id' => $work->id]);
    $preview = $this->post(route('staff.metadata-quality.preview', ['work' => $work]));
    $preview->assertOk();
    $confirmation = ['preview_token' => $preview->viewData('token'), 'fields' => ['title']];

    $this->patch(route('staff.metadata-quality.update', ['work' => $work]), $confirmation)
        ->assertRedirect(route('staff.metadata-quality.index'));
    expect($work->refresh()->title)->toBe('Fetched Title')
        ->and($work->description)->toBe('Retained description')
        ->and($work->raw)->toBe(['retained' => true]);
    $this->patch(route('staff.metadata-quality.update', ['work' => $work]), $confirmation)
        ->assertSessionHasErrors('preview_token');
});

test('applying a refresh with a preview token issued for a different work is rejected and does not touch that other work', function (): void {
    config(['api-keys.tmdb' => 'test-key']);

    Http::fake([
        'https://api.themoviedb.org/3/movie/*' => Http::response([
            'title' => 'Fetched Title', 'overview' => 'Fetched overview',
        ], 200),
    ]);

    $category = metadataQualityMovieCategory();

    $workA = MediaWork::create([
        'identity_key' => 'movie:tmdb:111',
        'kind'         => 'movie',
        'source'       => 'TMDB',
        'source_id'    => '111',
        'title'        => 'Work A Title',
    ]);
    $workB = MediaWork::create([
        'identity_key' => 'movie:tmdb:222',
        'kind'         => 'movie',
        'source'       => 'TMDB',
        'source_id'    => '222',
        'title'        => 'Work B Title',
    ]);
    Torrent::factory()->create(['category_id' => $category->id, 'media_work_id' => $workA->id]);
    Torrent::factory()->create(['category_id' => $category->id, 'media_work_id' => $workB->id]);

    $preview = $this->post(route('staff.metadata-quality.preview', ['work' => $workA]));
    $preview->assertOk();
    $token = $preview->viewData('token');

    $this->patch(route('staff.metadata-quality.update', ['work' => $workB]), [
        'preview_token' => $token,
        'fields'        => ['title'],
    ])->assertSessionHasErrors('preview_token');

    $workB->refresh();
    expect($workB->title)->toBe('Work B Title');
});
