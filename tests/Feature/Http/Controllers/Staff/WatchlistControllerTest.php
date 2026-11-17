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

use App\Models\Watchlist;

test('destroy returns an ok response', function (): void {
    $watchlist = Watchlist::factory()->create();

    $this->delete(route('staff.watchlist.destroy', [$watchlist]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'Successfully stopped watching user');
    $this->assertModelMissing($watchlist);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.watchlist.index'))
        ->assertOk()
        ->assertViewIs('Staff.watchlist.index');
});

test('store returns an ok response', function (): void {
    $watchedUser = Watchlist::factory()->make()->makeHidden('staff_id');

    $this->post(route('staff.watchlist.store'), $watchedUser->toArray())
        ->assertRedirectBack()
        ->assertSessionHas('success', 'User successfully being watched');

    $this->assertDatabaseHas('watchlists', $watchedUser->toArray());
});
