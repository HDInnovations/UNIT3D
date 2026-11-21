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

use App\Models\TorrentRequest;
use App\Models\TorrentRequestBounty;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $torrentRequest = TorrentRequest::factory()->create(['approved_when' => null]);
    $user = User::factory()->create(['seedbonus' => 100000]);
    $data = [
        'seedbonus' => 10000,
        'anon'      => random_int(0, 1),
    ];

    $this->actingAs($user)
        ->post(route('requests.bounties.store', [$torrentRequest]), $data)
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.added-bonus'));

    $this->assertDatabaseHas('request_bounty', [
        'request_id' => $torrentRequest->id,
        ...$data,
    ]);
});

test('update returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->create();
    $bounty = TorrentRequestBounty::factory()->for($torrentRequest, 'request')->for($user)->create();
    $newBounty = TorrentRequestBounty::factory()->for($torrentRequest, 'request')->for($user)->make();

    $this->actingAs($user)
        ->patch(route('requests.bounties.update', [$torrentRequest, $bounty]), $newBounty->toArray())
        ->assertRedirectBack()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('request_bounty', [
        'id'   => $bounty->id,
        'anon' => $newBounty->anon,
    ]);
});
