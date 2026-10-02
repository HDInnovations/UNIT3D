<?php

declare(strict_types=1);

use App\Helpers\Bencode;
use App\Models\Category;
use App\Models\Type;
use App\Models\User;
use App\Services\Metadata\MetadataSelectionStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('publishes distinct variants under shared metadata and consumes the successful uploader draft', function (): void {
    Http::fake();
    Storage::fake('torrent-files');
    Illuminate\Support\Facades\Queue::fake();
    config(['announce.external_tracker.is_enabled' => false]);
    User::factory()->create(['id' => User::SYSTEM_USER_ID]);
    $group = App\Models\Group::factory()->create(['is_trusted' => false, 'is_modo' => false]);
    $user = User::factory()->create(['can_upload' => true, 'group_id' => $group->id]);
    $category = Category::factory()->create([
        'movie_meta' => false, 'tv_meta' => false, 'game_meta' => false,
        'music_meta' => true, 'book_meta' => false, 'no_meta' => false,
    ]);
    $type = Type::factory()->create(['name' => 'Lossless']);
    $mbid = 'df4b48f5-77fe-499d-a6a2-ca83d186beb8';
    $token = app(MetadataSelectionStore::class)->remember($user, $category, [
        'title' => 'Mockingbird', 'description' => '', 'source' => 'MusicBrainz',
        'source_url' => null, 'cover_url' => null, 'identifiers' => ['musicbrainz_id' => $mbid],
        'raw' => ['id' => $mbid, 'title' => 'Mockingbird', 'primary-type' => 'Single'],
    ]);
    $lossyType = Type::factory()->create(['name' => 'Lossy']);
    $draft = App\Models\UploadDraft::create(['user_id' => $user->id, 'title' => 'My draft', 'fields' => ['name' => 'Mockingbird']]);
    $fields = [
        'name' => 'Mockingbird', 'description' => 'Uploader notes', 'category_id' => $category->id,
        'type_id' => $type->id, 'musicbrainz_id' => $mbid, 'metadata_selection_token' => $token,
        'anon' => false, 'personal_release' => false,
    ];
    foreach (['a', 'b'] as $index => $piece) {
        $file = UploadedFile::fake()->createWithContent('quality.torrent', Bencode::bencode([
            'info' => ['name' => $index === 0 ? 'Mockingbird.flac' : 'Mockingbird.mp3', 'piece length' => 16384, 'length' => $index === 0 ? 12345 : 8000, 'pieces' => str_repeat($piece, 20)],
        ]));
        $payload = $fields + ['torrent' => $file];
        $payload['type_id'] = $index === 0 ? $type->id : $lossyType->id;
        if ($index === 0) $payload['upload_draft_id'] = $draft->id;
        $this->actingAs($user)->post(route('torrents.store'), $payload)->assertRedirect();
    }
    $variants = App\Models\Torrent::withoutGlobalScope(App\Models\Scopes\ApprovedScope::class)->orderBy('id')->get();
    expect($variants)->toHaveCount(2)
        ->and($variants[0]->media_work_id)->toBe($variants[1]->media_work_id)
        ->and($variants[0]->info_hash)->not->toBe($variants[1]->info_hash)
        ->and($variants[0]->type_id)->not->toBe($variants[1]->type_id)
        ->and($variants->pluck('size')->all())->toEqual([12345, 8000])
        ->and($variants[0]->description)->toBe('Uploader notes')
        ->and($variants[0]->mediaWork->title)->toBe('Mockingbird');
    $this->assertDatabaseMissing('upload_drafts', ['id' => $draft->id]);
    $this->assertDatabaseCount('media_works', 1);
});

it('rejects forbidden uploads before validating files or creating tracker records', function (): void {
    Http::fake();
    Storage::fake('torrent-files');
    $user = User::factory()->create(['can_upload' => false]);

    $this->actingAs($user)->postJson(route('torrents.store'), [])->assertForbidden();
    $this->assertDatabaseCount('torrents', 0);
    expect(Storage::disk('torrent-files')->allFiles())->toBe([]);
    Http::assertNothingSent();
});

it('rejects a provider selection from another uploader before writing the torrent', function (): void {
    Http::fake();
    Storage::fake('torrent-files');
    $owner = User::factory()->create(['can_upload' => true]);
    $uploader = User::factory()->create(['can_upload' => true]);
    $category = Category::factory()->create([
        'movie_meta' => false, 'tv_meta' => false, 'game_meta' => false,
        'music_meta' => true, 'book_meta' => false, 'no_meta' => false,
    ]);
    $type = Type::factory()->create(['name' => 'Lossless']);
    $mbid = 'df4b48f5-77fe-499d-a6a2-ca83d186beb8';
    $token = app(MetadataSelectionStore::class)->remember($owner, $category, [
        'title' => 'Eminem - Mockingbird', 'description' => '', 'description_language' => null,
        'source' => 'MusicBrainz', 'source_url' => null, 'cover_url' => null, 'warning' => null,
        'identifiers' => ['musicbrainz_id' => $mbid],
        'raw' => ['id' => $mbid, 'title' => 'Mockingbird', 'primary-type' => 'Single'],
        'details' => [],
    ]);
    $file = UploadedFile::fake()->createWithContent('selection.torrent', Bencode::bencode([
        'announce' => 'https://example.test/announce',
        'info' => ['name' => 'Mockingbird.flac', 'piece length' => 16384, 'length' => 12345, 'pieces' => str_repeat('x', 20)],
    ]));

    $this->actingAs($uploader)->postJson(route('torrents.store'), [
        'torrent' => $file, 'name' => 'Mockingbird FLAC', 'description' => 'My release notes',
        'category_id' => $category->id, 'type_id' => $type->id, 'musicbrainz_id' => $mbid,
        'metadata_selection_token' => $token, 'anon' => false, 'personal_release' => false,
        'internal' => false, 'free' => 0, 'refundable' => false,
    ])->assertUnprocessable()->assertJsonValidationErrors('metadata_selection_token');

    $this->assertDatabaseCount('torrents', 0);
    expect(Storage::disk('torrent-files')->allFiles())->toBe([]);
    Http::assertNothingSent();
});
