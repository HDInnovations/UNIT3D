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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use App\Models\MediaLanguage;
use App\Models\Subtitle;
use App\Models\Torrent;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('create returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $mediaLanguages = MediaLanguage::factory()->times(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('subtitles.create', ['torrent_id' => $torrent->id]))
        ->assertOk()
        ->assertViewIs('subtitle.create')
        ->assertViewHas('torrent', $torrent)
        ->assertViewHas('media_languages');
});

test('destroy returns an ok response', function (): void {
    $subtitle = Subtitle::factory()->create();
    $response = $this->actingAs($subtitle->user)->delete(route('subtitles.destroy', [$subtitle]))
        ->assertRedirectToRoute('torrents.show', [$subtitle->torrent_id]);
    $this->assertModelMissing($subtitle);
});

test('download returns an ok response', function (): void {
    $user = User::factory()->state(['can_download' => true])->create();
    $subtitle = Subtitle::factory()->create();

    Storage::fake('subtitle-files');
    Storage::disk('subtitle-files')->put($subtitle->file_name, 'Fake subtitles');

    $response = $this->actingAs($user)->get(route('subtitles.download', [$subtitle]))
        ->assertOk()
        ->assertDownload();
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('subtitles.index'))
        ->assertOk()
        ->assertViewIs('subtitle.index');
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $user = User::factory()->create();
    $torrent = Torrent::factory()->create();
    $subtitle = Subtitle::factory()->for($torrent)->make();

    $response = $this->actingAs($user)->post(route('subtitles.store'), [
        ...$subtitle->toArray(),
        'subtitle_file' => UploadedFile::fake()->create('subtitles.srt', 30),
    ])
        ->assertRedirectToRoute('torrents.show', [$torrent]);
});

test('update returns an ok response', function (): void {
    $subtitle = Subtitle::factory()->create();
    $newSubtitle = Subtitle::factory()->make();

    $response = $this->actingAs($subtitle->user)->patch(route('subtitles.update', [$subtitle]), $newSubtitle->toArray())
        ->assertRedirectToRoute('torrents.show', [$subtitle->torrent_id]);
});
