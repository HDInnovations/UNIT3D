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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\Torrent;
use App\Models\TorrentTip;
use App\Models\User;

test('index returns an ok response', function (): void {
    $user = User::factory()->create();
    $bonTransactions = TorrentTip::factory()->for($user)->times(3)->create();

    $this->actingAs($user)->get(route('users.torrent_tips.index', [$user]))
        ->assertOk()
        ->assertViewIs('user.torrent-tip.index')
        ->assertViewHas('user', $user)
        ->assertViewHas('tips')
        ->assertViewHas('bon')
        ->assertViewHas('sentTips')
        ->assertViewHas('receivedTips');
});

test('store returns an ok response', function (): void {
    $torrent = Torrent::factory()->create();
    $user = User::factory()->create(['seedbonus' => 1000]);
    $data = [
        'torrent_id' => $torrent->id,
        'bon'        => 1000,
    ];

    $this->actingAs($user)->post(route('users.torrent_tips.store', [$user]), $data)
        ->assertRedirectBack()
        ->assertSessionHas('success', __('bon.success-tip'));

    $this->assertDatabaseHas('torrent_tips', $data);
});
