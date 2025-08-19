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

use App\Models\User;
use Database\Seeders\GroupSeeder;
use Database\Seeders\UserSeeder;

test('destroy returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create();
    $userToFollow = User::factory()->create();

    $followResponse = $this->actingAs($user)->post(route('users.followers.store', ['user' => $userToFollow]));
    $followResponse->assertRedirect(route('users.show', ['user' => $userToFollow]))
        ->assertSessionHas('success', \sprintf('You are now following %s', $userToFollow->username));

    $response = $this->actingAs($user)->delete(route('users.followers.destroy', ['user' => $userToFollow]));
    $response->assertRedirectBack()
        ->assertSessionHas('success', \sprintf('You are no longer following %s', $userToFollow->username));

    $this->assertDatabaseMissing('follows', [
        'user_id'   => $user->id,
        'target_id' => $userToFollow->id,
    ]);
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.followers.index', [$user]));
    $response->assertOk();
    $response->assertViewIs('user.follower.index');
    $response->assertViewHas('followers');
    $response->assertViewHas('user', $user);
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create();
    $userToFollow = User::factory()->create();

    $response = $this->actingAs($user)->post(route('users.followers.store', ['user' => $userToFollow]));
    $response->assertRedirect(route('users.show', ['user' => $userToFollow]))
        ->assertSessionHas('success', \sprintf('You are now following %s', $userToFollow->username));
});
