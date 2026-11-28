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

use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;

test('store returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $torrent = Torrent::factory()->create();
    $user = User::factory()->create();
    $data = [
        'torrent_id'  => $torrent->id,
        'filled_anon' => random_int(0, 1),
    ];

    $response = $this->actingAs($user)->post(route('requests.fills.store', [$torrentRequest]), $data)
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.pending-approval'));

    $this->assertDatabaseHas('requests', [
        'id' => $torrentRequest->id,
        ...$data,
    ]);
});

test('destroy returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->moderator()->create();

    $response = $this->actingAs($user)->delete(route('requests.fills.destroy', [$torrentRequest]))
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.request-reset'));

    $this->assertDatabaseHas('requests', [
        'id'          => $torrentRequest->id,
        'filled_by'   => null,
        'filled_when' => null,
        'torrent_id'  => null,
    ]);
});
