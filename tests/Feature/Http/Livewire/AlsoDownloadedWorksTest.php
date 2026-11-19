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

use App\Http\Livewire\AlsoDownloadedWorks;
use App\Models\Category;
use App\Models\History;
use App\Models\IgdbGame;
use App\Models\Torrent;
use App\Models\User;
use Livewire\Livewire;

test('also downloaded works lists games downloaded by the same users', function (): void {
    $category = Category::factory()->create([
        'no_meta'    => false,
        'music_meta' => false,
        'game_meta'  => true,
        'tv_meta'    => false,
        'movie_meta' => false,
    ]);

    $game = IgdbGame::query()->create(['id' => 1001, 'name' => 'Current Game']);
    $otherGame = IgdbGame::query()->create(['id' => 1002, 'name' => 'Other Game']);

    $torrent = Torrent::factory()->create(['category_id' => $category->id, 'igdb' => $game->id]);
    $otherTorrent = Torrent::factory()->create(['category_id' => $category->id, 'igdb' => $otherGame->id]);

    $user = User::factory()->create();

    foreach ([$torrent, $otherTorrent] as $downloadedTorrent) {
        History::factory()->create([
            'user_id'    => $user->id,
            'torrent_id' => $downloadedTorrent->id,
            'created_at' => now()->addDay(),
        ]);
    }

    Livewire::withoutLazyLoading()
        ->actingAs($user)
        ->test(AlsoDownloadedWorks::class, ['work' => $game])
        ->assertOk()
        ->assertSee('Other Game')
        ->assertDontSee('Current Game');
});
