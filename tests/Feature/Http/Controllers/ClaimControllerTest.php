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
use App\Models\TorrentRequestClaim;
use App\Models\User;

test('store returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->create();
    $claim = TorrentRequestClaim::factory()->for($torrentRequest, 'request')->for($user)->make();

    $this->actingAs($user)->post(route('requests.claims.store', [$torrentRequest]), $claim->toArray())
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.claimed-success'));

    $this->assertDatabaseHas('request_claims', $claim->toArray());
});

test('destroy returns an ok response', function (): void {
    $torrentRequest = TorrentRequest::factory()->create();
    $user = User::factory()->create();
    $claim = TorrentRequestClaim::factory()
        ->for($torrentRequest, 'request')
        ->for($user)
        ->create();

    $this->actingAs($user)->delete(route('requests.claims.destroy', [$torrentRequest, $claim]))
        ->assertRedirectToRoute('requests.show', [$torrentRequest])
        ->assertSessionHas('success', __('request.unclaimed-success'));
    $this->assertModelMissing($claim);
});
