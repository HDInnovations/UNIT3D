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

use App\Models\BonExchange;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('create returns an ok response', function (): void {
    $user = User::factory()->create();
    $bonExchanges = BonExchange::factory()->times(10)->create();

    $response = $this->actingAs($user)->get(route('users.transactions.create', [$user]))
        ->assertOk()
        ->assertViewIs('user.transaction.create')
        ->assertViewHas('user', $user)
        ->assertViewHas('bon')
        ->assertViewHas('activefl')
        ->assertViewHas('items');
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $user = User::factory()->create([
        'seedbonus'  => 10000,
        'invites'    => 0,
        'downloaded' => 10000,
    ]);
    $bonExchange = BonExchange::factory()->create([
        'cost'  => 1000,
        'value' => 1000,
    ]);

    $response = $this->actingAs($user)->post(route('users.transactions.store', [$user]), [
        'exchange' => $bonExchange->id,
    ])
        ->assertRedirectBack()
        ->assertSessionHas('success', __('bon.success'));
});
