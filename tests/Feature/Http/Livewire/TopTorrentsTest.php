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

use App\Http\Livewire\TopTorrents;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Torrent;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('newest top torrents render as a larger poster grid', function (): void {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'no_meta'    => true,
        'music_meta' => false,
        'game_meta'  => false,
        'tv_meta'    => false,
        'movie_meta' => false,
    ]);
    $type = Type::factory()->create();
    $resolution = Resolution::factory()->create();

    for ($i = 1; $i <= 16; $i++) {
        Torrent::factory()->create([
            'name'          => \sprintf('Newest %02d', $i),
            'category_id'   => $category->id,
            'type_id'       => $type->id,
            'resolution_id' => $resolution->id,
        ]);
    }

    Livewire::actingAs($user)
        ->test(TopTorrents::class)
        ->assertSet('tab', 'newest')
        ->assertSee('torrent-search--poster__results', false)
        ->assertSee('Newest 16')
        ->assertSee('Newest 02')
        ->assertDontSee('Newest 01');
});

test('non-newest top torrent tabs keep the compact row table', function (): void {
    $user = User::factory()->create();

    Torrent::factory()->count(6)->create([
        'seeders' => 10,
    ]);

    Livewire::actingAs($user)
        ->test(TopTorrents::class)
        ->set('tab', 'seeded')
        ->assertSee('data-table-wrapper', false)
        ->assertDontSee('torrent-search--poster__results', false);
});
