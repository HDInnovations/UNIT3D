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

use App\Enums\UserGroup;
use App\Models\User;
use Database\Seeders\GroupSeeder;

test('edit returns a redirect to confirm password', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.email.edit', [$user]));

    $response->assertRedirect(route('password.confirm'));
});

test('edit returns a redirect to confirm two factor code', function (): void {
    $user = User::factory(['two_factor_confirmed_at' => now()->subSeconds(301)])->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => now()->unix()])
        ->get(route('users.email.edit', [$user]));

    $response->assertRedirect(route('two-factor.confirm'));
});

test('edit returns an ok response', function (): void {
    $user = User::factory(['two_factor_confirmed_at' => now()])->create();

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at'   => now()->unix(),
            'auth.two_factor_confirmed_at' => now()->unix(),
        ])
        ->get(route('users.email.edit', [$user]));

    $response->assertOk();
    $response->assertViewIs('user.email.edit');
    $response->assertViewHas('user', $user);
});

test('edit aborts with a 403', function (): void {
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create([
        'group_id' => UserGroup::MODERATOR->value,
    ]);

    $authUser = User::factory()->create([
        'group_id'                => UserGroup::USER->value,
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->actingAs($authUser)
        ->withSession([
            'auth.password_confirmed_at'   => now()->unix(),
            'auth.two_factor_confirmed_at' => now()->unix(),
        ])
        ->get(route('users.email.edit', [$user]));

    $response->assertForbidden();
});

test('update returns a redirect to confirm password', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('users.email.update', [$user]));

    $response->assertRedirect(route('password.confirm'));
});

test('update returns a redirect to confirm two factor code', function (): void {
    $user = User::factory(['two_factor_confirmed_at' => now()->subSeconds(301)])->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => now()->unix()])
        ->patch(route('users.email.update', [$user]));

    $response->assertRedirect(route('two-factor.confirm'));
});

test('update returns an ok response', function (): void {
    $user = User::factory(['two_factor_confirmed_at' => now()])->create();

    $response = $this->actingAs($user)
        ->withSession([
            'auth.password_confirmed_at'   => now()->unix(),
            'auth.two_factor_confirmed_at' => now()->unix(),
        ])
        ->patch(route('users.email.update', [$user]), [
            'email' => fake()->unique()->freeEmail,
        ]);

    $response->assertRedirect(route('users.email.edit', ['user' => $user]))
        ->assertSessionHas('success', 'Your email was updated successfully.');
});

test('update aborts with a 403', function (): void {
    $this->seed(GroupSeeder::class);

    $user = User::factory()->create([
        'group_id' => UserGroup::MODERATOR->value,
    ]);

    $authUser = User::factory()->create([
        'group_id'                => UserGroup::USER->value,
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->actingAs($authUser)
        ->withSession([
            'auth.password_confirmed_at'   => now()->unix(),
            'auth.two_factor_confirmed_at' => now()->unix(),
        ])
        ->patch(route('users.email.update', [$user]));

    $response->assertForbidden();
});
