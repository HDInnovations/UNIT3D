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

use App\Helpers\Bencode;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Group;
use App\Models\Internal;
use App\Models\InternalUser;
use App\Models\Region;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('create returns an ok response', function (): void {
    Category::factory()->times(3)->create();
    Type::factory()->times(3)->create();
    Resolution::factory()->times(3)->create();
    Region::factory()->times(3)->create();
    Distributor::factory()->times(3)->create();
    $user = User::factory()->create(['can_upload' => true]);

    $this->actingAs($user)
        ->get(route('torrents.create'))
        ->assertOk()
        ->assertViewIs('torrent.create')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('regions')
        ->assertViewHas('distributors')
        ->assertViewHas('user', $user)
        ->assertViewHas('category_id')
        ->assertViewHas('title')
        ->assertViewHas('imdb')
        ->assertViewHas('movieId')
        ->assertViewHas('tvId')
        ->assertViewHas('mal')
        ->assertViewHas('tvdb')
        ->assertViewHas('igdb');
});

test('destroy returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $user = User::factory()->moderator()->create();

    $this->actingAs($user)
        ->delete(route('torrents.destroy', ['id' => $torrent->id]), [
            'message' => 'test'
        ])
        ->assertRedirectToRoute('torrents.index')
        ->assertSessionHas('success');

    $this->assertSoftDeleted($torrent);
});

test('edit returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    Category::factory()->times(3)->create();
    Type::factory()->times(3)->create();
    Resolution::factory()->times(3)->create();
    Region::factory()->times(3)->create();
    Distributor::factory()->times(3)->create();
    $user = User::factory()->moderator()->create();

    $this->actingAs($user)
        ->get(route('torrents.edit', ['id' => $torrent->id]))
        ->assertOk()
        ->assertViewIs('torrent.edit')
        ->assertViewHas('categories')
        ->assertViewHas('types')
        ->assertViewHas('resolutions')
        ->assertViewHas('regions')
        ->assertViewHas('distributors')
        ->assertViewHas('keywords')
        ->assertViewHas('torrent', $torrent)
        ->assertViewHas('user', $user);
});

test('index returns an ok response', function (): void {
    $this->markTestIncomplete('Requires meilisearch dependency');

    Torrent::factory()->times(3)->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('torrents.index'))
        ->assertOk()
        ->assertViewIs('torrent.index');
});

test('show returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('torrents.show', ['id' => $torrent->id]))
        ->assertOk()
        ->assertViewIs('torrent.show')
        ->assertViewHas('torrent', $torrent)
        ->assertViewHas('user', $user)
        ->assertViewHas('canEdit')
        ->assertViewHas('personal_freeleech')
        ->assertViewHas('mediaInfo')
        ->assertViewHas('fileTree')
        ->assertViewHas('alsoDownloaded');
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->create(['can_upload' => true]);
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->make();

    Storage::fake('torrent-files');
    Storage::fake('temporary-nfos');

    Http::fake();

    $this->actingAs($user)
        ->post(route('torrents.store'), [
            'torrent' => UploadedFile::fake()->createWithContent('torrent.torrent', Bencode::bencode([
                'info' => [
                    'files' => [[
                        'length' => 2097152,
                        'path'   => [
                            'filename.txt',
                        ],
                    ]],
                    'name'         => 'filename.txt',
                    'piece length' => 2097152,
                    'pieces'       => '01234567890123456789',
                ]
            ])),
            'name'                 => $torrent->name,
            'description'          => $torrent->description,
            'category_id'          => $torrent->category_id,
            'type_id'              => $torrent->type_id,
            'nfo'                  => UploadedFile::fake()->createWithContent('nfo.nfo', 'test'),
            'mediainfo'            => $torrent->mediainfo,
            'bdinfo'               => $torrent->bdinfo,
            'resolution_id'        => $torrent->resolution_id,
            'region_id'            => $torrent->region_id,
            'distributor_id'       => $torrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $torrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $torrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $torrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $torrent->mal,
            'season_number'        => $torrent->season_number,
            'episode_number'       => $torrent->episode_number,
            'anon'                 => $torrent->anon,
            'personal_release'     => $torrent->personal_release,
            'internal'             => $torrent->internal,
            'free'                 => $torrent->free,
            'refundable'           => $torrent->refundable,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Your torrent file is ready to be downloaded and seeded!');

    $this->assertDatabaseHas('torrents', [
        'name'             => $torrent->name,
        'description'      => $torrent->description,
        'category_id'      => $torrent->category_id,
        'type_id'          => $torrent->type_id,
        'mediainfo'        => $torrent->mediainfo,
        'bdinfo'           => $torrent->bdinfo,
        'resolution_id'    => $torrent->resolution_id,
        'region_id'        => $torrent->region_id,
        'distributor_id'   => $torrent->distributor_id,
        'imdb'             => $torrent->imdb,
        'tvdb'             => $torrent->tvdb,
        'tmdb_tv_id'       => $torrent->tmdb_tv_id,
        'mal'              => $torrent->mal,
        'season_number'    => $torrent->season_number,
        'episode_number'   => $torrent->episode_number,
        'anon'             => $torrent->anon,
        'personal_release' => $torrent->personal_release,
        'internal'         => false,
        'free'             => 0,
        'refundable'       => false,
    ]);
});

test('staff store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->moderator()->create(['can_upload' => true]);
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->make();

    Storage::fake('torrent-files');
    Storage::fake('temporary-nfos');

    Http::fake();

    $this->actingAs($user)
        ->post(route('torrents.store'), [
            'torrent' => UploadedFile::fake()->createWithContent('torrent.torrent', Bencode::bencode([
                'info' => [
                    'files' => [[
                        'length' => 2097152,
                        'path'   => [
                            'filename.txt',
                        ],
                    ]],
                    'name'         => 'filename.txt',
                    'piece length' => 2097152,
                    'pieces'       => '01234567890123456789',
                ]
            ])),
            'name'                 => $torrent->name,
            'description'          => $torrent->description,
            'category_id'          => $torrent->category_id,
            'type_id'              => $torrent->type_id,
            'nfo'                  => UploadedFile::fake()->createWithContent('nfo.nfo', 'test'),
            'mediainfo'            => $torrent->mediainfo,
            'bdinfo'               => $torrent->bdinfo,
            'resolution_id'        => $torrent->resolution_id,
            'region_id'            => $torrent->region_id,
            'distributor_id'       => $torrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $torrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $torrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $torrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $torrent->mal,
            'season_number'        => $torrent->season_number,
            'episode_number'       => $torrent->episode_number,
            'anon'                 => $torrent->anon,
            'personal_release'     => $torrent->personal_release,
            'internal'             => $torrent->internal,
            'free'                 => $torrent->free,
            'refundable'           => $torrent->refundable,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Your torrent file is ready to be downloaded and seeded!');

    $this->assertDatabaseHas('torrents', [
        'name'             => $torrent->name,
        'description'      => $torrent->description,
        'category_id'      => $torrent->category_id,
        'type_id'          => $torrent->type_id,
        'mediainfo'        => $torrent->mediainfo,
        'bdinfo'           => $torrent->bdinfo,
        'resolution_id'    => $torrent->resolution_id,
        'region_id'        => $torrent->region_id,
        'distributor_id'   => $torrent->distributor_id,
        'imdb'             => $torrent->imdb,
        'tvdb'             => $torrent->tvdb,
        'tmdb_tv_id'       => $torrent->tmdb_tv_id,
        'mal'              => $torrent->mal,
        'season_number'    => $torrent->season_number,
        'episode_number'   => $torrent->episode_number,
        'anon'             => $torrent->anon,
        'personal_release' => $torrent->personal_release,
        'internal'         => $torrent->internal,
        'free'             => $torrent->free,
        'refundable'       => $torrent->refundable,
    ]);
});

test('update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->create([
        'created_at' => now()->subHour(),
    ]);
    $newTorrent = Torrent::factory()->for($user)->for($category)->make();

    Http::fake();

    $this->actingAs($user)
        ->patch(route('torrents.update', ['id' => $torrent]), [
            'name'                 => $newTorrent->name,
            'description'          => $newTorrent->description,
            'category_id'          => $newTorrent->category_id,
            'type_id'              => $newTorrent->type_id,
            'mediainfo'            => $newTorrent->mediainfo,
            'bdinfo'               => $newTorrent->bdinfo,
            'resolution_id'        => $newTorrent->resolution_id,
            'region_id'            => $newTorrent->region_id,
            'distributor_id'       => $newTorrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $newTorrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $newTorrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $newTorrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $newTorrent->mal,
            'season_number'        => $newTorrent->season_number,
            'episode_number'       => $newTorrent->episode_number,
            'anon'                 => $newTorrent->anon,
            'personal_release'     => $newTorrent->personal_release,
            'internal'             => $newTorrent->internal,
            'free'                 => $newTorrent->free,
            'refundable'           => $newTorrent->refundable,
        ])
        ->assertRedirectToRoute('torrents.show', ['id' => $torrent->id])
        ->assertSessionHas('success', 'Successfully edited!');

    $this->assertDatabaseHas('torrents', [
        'id'               => $torrent->id,
        'name'             => $newTorrent->name,
        'description'      => $newTorrent->description,
        'category_id'      => $newTorrent->category_id,
        'type_id'          => $newTorrent->type_id,
        'mediainfo'        => $newTorrent->mediainfo,
        'bdinfo'           => $newTorrent->bdinfo,
        'resolution_id'    => $newTorrent->resolution_id,
        'region_id'        => $newTorrent->region_id,
        'distributor_id'   => $newTorrent->distributor_id,
        'imdb'             => $newTorrent->imdb,
        'tvdb'             => $newTorrent->tvdb,
        'tmdb_tv_id'       => $newTorrent->tmdb_tv_id,
        'mal'              => $newTorrent->mal,
        'season_number'    => $newTorrent->season_number,
        'episode_number'   => $newTorrent->episode_number,
        'anon'             => $newTorrent->anon,
        'personal_release' => $newTorrent->personal_release,
        'internal'         => $torrent->internal,
        'free'             => $torrent->free,
        'refundable'       => $torrent->refundable,
    ]);
});

test('editor update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $editor = User::factory()->create([
        'group_id' => Group::factory()->create(['is_editor' => true]),
    ]);
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->create([
        'created_at' => now()->subHour(),
    ]);
    $newTorrent = Torrent::factory()->for($user)->for($category)->make();

    Http::fake();

    $this->actingAs($editor)
        ->patch(route('torrents.update', ['id' => $torrent]), [
            'name'                 => $newTorrent->name,
            'description'          => $newTorrent->description,
            'category_id'          => $newTorrent->category_id,
            'type_id'              => $newTorrent->type_id,
            'mediainfo'            => $newTorrent->mediainfo,
            'bdinfo'               => $newTorrent->bdinfo,
            'resolution_id'        => $newTorrent->resolution_id,
            'region_id'            => $newTorrent->region_id,
            'distributor_id'       => $newTorrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $newTorrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $newTorrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $newTorrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $newTorrent->mal,
            'season_number'        => $newTorrent->season_number,
            'episode_number'       => $newTorrent->episode_number,
            'anon'                 => $newTorrent->anon,
            'personal_release'     => $newTorrent->personal_release,
            'internal'             => $newTorrent->internal,
            'free'                 => $newTorrent->free,
            'refundable'           => $newTorrent->refundable,
        ])
        ->assertRedirectToRoute('torrents.show', ['id' => $torrent->id])
        ->assertSessionHas('success', 'Successfully edited!');

    $this->assertDatabaseHas('torrents', [
        'id'               => $torrent->id,
        'name'             => $newTorrent->name,
        'description'      => $newTorrent->description,
        'category_id'      => $newTorrent->category_id,
        'type_id'          => $newTorrent->type_id,
        'mediainfo'        => $newTorrent->mediainfo,
        'bdinfo'           => $newTorrent->bdinfo,
        'resolution_id'    => $newTorrent->resolution_id,
        'region_id'        => $newTorrent->region_id,
        'distributor_id'   => $newTorrent->distributor_id,
        'imdb'             => $newTorrent->imdb,
        'tvdb'             => $newTorrent->tvdb,
        'tmdb_tv_id'       => $newTorrent->tmdb_tv_id,
        'mal'              => $newTorrent->mal,
        'season_number'    => $newTorrent->season_number,
        'episode_number'   => $newTorrent->episode_number,
        'anon'             => $torrent->anon,
        'personal_release' => $torrent->personal_release,
        'internal'         => $torrent->internal,
        'free'             => $torrent->free,
        'refundable'       => $torrent->refundable,
    ]);
});

test('internal update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->create();
    $internal = Internal::factory()->create();
    InternalUser::factory()->for($user)->for($internal)->create();
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->create();
    $newTorrent = Torrent::factory()->for($user)->for($category)->make();

    Http::fake();

    $this->actingAs($user)
        ->patch(route('torrents.update', ['id' => $torrent]), [
            'name'                 => $newTorrent->name,
            'description'          => $newTorrent->description,
            'category_id'          => $newTorrent->category_id,
            'type_id'              => $newTorrent->type_id,
            'mediainfo'            => $newTorrent->mediainfo,
            'bdinfo'               => $newTorrent->bdinfo,
            'resolution_id'        => $newTorrent->resolution_id,
            'region_id'            => $newTorrent->region_id,
            'distributor_id'       => $newTorrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $newTorrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $newTorrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $newTorrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $newTorrent->mal,
            'season_number'        => $newTorrent->season_number,
            'episode_number'       => $newTorrent->episode_number,
            'anon'                 => $newTorrent->anon,
            'personal_release'     => $newTorrent->personal_release,
            'internal'             => $newTorrent->internal,
            'free'                 => $newTorrent->free,
            'refundable'           => $newTorrent->refundable,
        ])
        ->assertRedirectToRoute('torrents.show', ['id' => $torrent->id])
        ->assertSessionHas('success', 'Successfully edited!');

    $this->assertDatabaseHas('torrents', [
        'id'               => $torrent->id,
        'name'             => $newTorrent->name,
        'description'      => $newTorrent->description,
        'category_id'      => $newTorrent->category_id,
        'type_id'          => $newTorrent->type_id,
        'mediainfo'        => $newTorrent->mediainfo,
        'bdinfo'           => $newTorrent->bdinfo,
        'resolution_id'    => $newTorrent->resolution_id,
        'region_id'        => $newTorrent->region_id,
        'distributor_id'   => $newTorrent->distributor_id,
        'imdb'             => $newTorrent->imdb,
        'tvdb'             => $newTorrent->tvdb,
        'tmdb_tv_id'       => $newTorrent->tmdb_tv_id,
        'mal'              => $newTorrent->mal,
        'season_number'    => $newTorrent->season_number,
        'episode_number'   => $newTorrent->episode_number,
        'anon'             => $newTorrent->anon,
        'personal_release' => $newTorrent->personal_release,
        'internal'         => $newTorrent->internal,
        'free'             => $newTorrent->free,
        'refundable'       => $newTorrent->refundable,
    ]);
});

test('staff update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(OccupationSeeder::class);
    $user = User::factory()->moderator()->create();
    $category = Category::factory()->create([
        'tv_meta'    => true,
        'movie_meta' => false,
        'game_meta'  => false,
        'music_meta' => false,
        'no_meta'    => false,
    ]);
    $torrent = Torrent::factory()->for($user)->for($category)->create();
    $newTorrent = Torrent::factory()->for($user)->for($category)->make();

    Http::fake();

    $this->actingAs($user)
        ->patch(route('torrents.update', ['id' => $torrent]), [
            'name'                 => $newTorrent->name,
            'description'          => $newTorrent->description,
            'category_id'          => $newTorrent->category_id,
            'type_id'              => $newTorrent->type_id,
            'mediainfo'            => $newTorrent->mediainfo,
            'bdinfo'               => $newTorrent->bdinfo,
            'resolution_id'        => $newTorrent->resolution_id,
            'region_id'            => $newTorrent->region_id,
            'distributor_id'       => $newTorrent->distributor_id,
            'title_exists_on_imdb' => true,
            'imdb'                 => $newTorrent->imdb,
            'tv_exists_on_tvdb'    => true,
            'tvdb'                 => $newTorrent->tvdb,
            'tv_exists_on_tmdb'    => true,
            'tmdb_tv_id'           => $newTorrent->tmdb_tv_id,
            'anime_exists_on_mal'  => true,
            'mal'                  => $newTorrent->mal,
            'season_number'        => $newTorrent->season_number,
            'episode_number'       => $newTorrent->episode_number,
            'anon'                 => $newTorrent->anon,
            'personal_release'     => $newTorrent->personal_release,
            'internal'             => $newTorrent->internal,
            'free'                 => $newTorrent->free,
            'refundable'           => $newTorrent->refundable,
        ])
        ->assertRedirectToRoute('torrents.show', ['id' => $torrent->id])
        ->assertSessionHas('success', 'Successfully edited!');

    $this->assertDatabaseHas('torrents', [
        'id'               => $torrent->id,
        'name'             => $newTorrent->name,
        'description'      => $newTorrent->description,
        'category_id'      => $newTorrent->category_id,
        'type_id'          => $newTorrent->type_id,
        'mediainfo'        => $newTorrent->mediainfo,
        'bdinfo'           => $newTorrent->bdinfo,
        'resolution_id'    => $newTorrent->resolution_id,
        'region_id'        => $newTorrent->region_id,
        'distributor_id'   => $newTorrent->distributor_id,
        'imdb'             => $newTorrent->imdb,
        'tvdb'             => $newTorrent->tvdb,
        'tmdb_tv_id'       => $newTorrent->tmdb_tv_id,
        'mal'              => $newTorrent->mal,
        'season_number'    => $newTorrent->season_number,
        'episode_number'   => $newTorrent->episode_number,
        'anon'             => $newTorrent->anon,
        'personal_release' => $newTorrent->personal_release,
        'internal'         => $newTorrent->internal,
        'free'             => $newTorrent->free,
        'refundable'       => $newTorrent->refundable,
    ]);
});
