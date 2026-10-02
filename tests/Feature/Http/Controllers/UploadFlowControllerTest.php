<?php

declare(strict_types=1);

use App\Helpers\Bencode;
use App\Models\Category;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\UploadDraft;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function uploadFlowUploader(): User
{
    return User::factory()->create(['can_upload' => true]);
}

function uploadFlowNoMetaCategory(): Category
{
    return Category::factory()->create([
        'name'       => 'Other',
        'movie_meta' => false,
        'tv_meta'    => false,
        'game_meta'  => false,
        'music_meta' => false,
        'book_meta'  => false,
        'no_meta'    => true,
    ]);
}

test('drafts are private owner scoped partial form snapshots with a strict allowlist', function (): void {
    $owner = uploadFlowUploader();
    $intruder = uploadFlowUploader();

    $response = $this->actingAs($owner)->postJson(route('torrents.drafts.store'), [
        'title'  => 'Night draft',
        'fields' => [
            'category_id'              => '1',
            'name'                     => 'Visible title',
            'description'              => 'Saved description',
            'anon'                     => '1',
            'media_work_id'            => '123',
            'metadata_selection_token' => '550e8400-e29b-41d4-a716-446655440000',
            '_token'                   => 'csrf-secret',
            'passkey'                  => 'private-passkey',
            'provider_raw'             => ['leak' => true],
        ],
    ]);

    $response->assertOk()
        ->assertJsonPath('draft.title', 'Night draft')
        ->assertJsonPath('draft.fields.name', 'Visible title')
        ->assertJsonPath('draft.fields.description', 'Saved description');

    expect($response->json('draft.fields'))
        ->not->toHaveKey('metadata_selection_token')
        ->not->toHaveKey('_token')
        ->not->toHaveKey('passkey')
        ->not->toHaveKey('provider_raw');

    $draftId = $response->json('draft.id');

    UploadDraft::create([
        'user_id' => $intruder->id,
        'title'   => 'Intruder draft',
        'fields'  => ['name' => 'Someone else'],
    ]);

    $this->flushSession();
    $this->actingAs($intruder)->getJson(route('torrents.drafts.show', $draftId))->assertNotFound();
    $this->actingAs($intruder)->deleteJson(route('torrents.drafts.destroy', $draftId))->assertNotFound();
    $this->actingAs($intruder)->postJson(route('torrents.drafts.store'), [
        'id'     => $draftId,
        'title'  => 'Overwrite attempt',
        'fields' => ['name' => 'Changed by intruder'],
    ])->assertNotFound();

    $this->actingAs($intruder)->getJson(route('torrents.drafts.index'))
        ->assertOk()
        ->assertJsonMissing(['title' => 'Night draft'])
        ->assertJsonFragment(['title' => 'Intruder draft']);

    $ownerDraft = UploadDraft::find($draftId);
    expect($ownerDraft)->not->toBeNull();
    expect($ownerDraft->title)->toBe('Night draft');
    expect($ownerDraft->fields)->toHaveKey('name', 'Visible title');
});

test('a malformed torrent preview returns a file validation error without publication', function (): void {
    Storage::fake('torrent-files');
    $user = uploadFlowUploader();
    $category = uploadFlowNoMetaCategory();
    $type = Type::factory()->create(['name' => 'Other']);
    $this->actingAs($user)->postJson(route('torrents.preview'), [
        'torrent' => UploadedFile::fake()->createWithContent('invalid.torrent', 'not bencoded metainfo'),
        'name' => 'Invalid upload', 'description' => 'Release notes',
        'category_id' => $category->id, 'type_id' => $type->id,
        'anon' => false, 'personal_release' => false,
    ])->assertUnprocessable()->assertJsonValidationErrors('torrent');
    expect(Torrent::query()->count())->toBe(0);
    expect(Storage::disk('torrent-files')->allFiles())->toBe([]);
});

test('preview validates the publication contract and renders without publication side effects', function (): void {
    Storage::fake('torrent-files');
    Storage::fake('torrent-covers');
    Storage::fake('torrent-banners');

    $user = uploadFlowUploader();
    $category = uploadFlowNoMetaCategory();
    $type = Type::factory()->create(['name' => 'Other']);
    $torrentFile = UploadedFile::fake()->createWithContent('preview.torrent', Bencode::bencode([
        'announce' => 'https://example.test/announce',
        'info' => [
            'name'         => 'Preview Folder',
            'piece length' => 16384,
            'length'       => 12345,
            'pieces'       => str_repeat('x', 20),
        ],
    ]));

    $response = $this->actingAs($user)->postJson(route('torrents.preview'), [
        'torrent'          => $torrentFile,
        'name'             => 'Preview Upload',
        'description'      => '[b]Real description[/b]',
        'category_id'      => $category->id,
        'type_id'          => $type->id,
        'anon'             => false,
        'personal_release' => false,
    ]);

    $response->assertOk()->assertJsonStructure(['html']);

    expect($response->json('html'))
        ->not->toBeEmpty()
        ->toContain('Preview Upload')
        ->toContain('Real description');

    expect(Torrent::query()->count())->toBe(0);
    expect(Storage::disk('torrent-files')->allFiles())->toBe([]);
    expect(Storage::disk('torrent-covers')->allFiles())->toBe([]);
    expect(Storage::disk('torrent-banners')->allFiles())->toBe([]);
});
