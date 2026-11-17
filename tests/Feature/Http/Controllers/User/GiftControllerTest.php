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

use App\Models\Gift;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Queue;

test('create returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.gifts.create', [$user]))
        ->assertOk()
        ->assertViewIs('user.gift.create')
        ->assertViewHas('user', $user)
        ->assertViewHas('bon');
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();
    $bonTransactions = Gift::factory()->for($user, 'sender')->times(3)->create();

    $response = $this->actingAs($user)->get(route('users.gifts.index', [$user]))
        ->assertOk()
        ->assertViewIs('user.gift.index')
        ->assertViewHas('user', $user)
        ->assertViewHas('gifts')
        ->assertViewHas('bon')
        ->assertViewHas('sentGifts')
        ->assertViewHas('receivedGifts');
});

test('store returns an ok response', function (): void {
    Queue::fake();
    $this->seed(UserSeeder::class);

    $user = User::factory()->state(['seedbonus' => 10000])->create();
    $gift = Gift::factory()->for($user, 'sender')->state(['bon' => 100])->make();

    $response = $this->actingAs($user)->post(route('users.gifts.store', [$user]), [
        'bon'                => $gift->bon,
        'recipient_username' => $gift->recipient->username,
        'message'            => $gift->message,
    ])
        ->assertRedirectBack()
        ->assertSessionHas('success', __('bon.gift-sent'));
});
