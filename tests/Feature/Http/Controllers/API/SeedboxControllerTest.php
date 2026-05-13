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

use App\Enums\AuthGuard;
use App\Models\Seedbox;
use App\Models\User;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;

use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    $this->withoutMiddleware(ThrottleRequestsWithRedis::class);
});

test('index returns the authenticated users seedboxes', function (): void {
    $user = User::factory()->create();
    $seedbox = Seedbox::factory()->create([
        'ip'      => '203.0.113.10',
        'name'    => 'HomeSeedbox',
        'user_id' => $user->id,
    ]);
    $otherSeedbox = Seedbox::factory()->create([
        'ip'   => '203.0.113.11',
        'name' => 'OtherSeedbox',
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->getJson('/api/user/seedboxes');

    $response->assertOk()
        ->assertJsonFragment([
            'id'   => $seedbox->id,
            'ip'   => '203.0.113.10',
            'name' => 'HomeSeedbox',
        ])
        ->assertJsonMissing([
            'id'   => $otherSeedbox->id,
            'ip'   => '203.0.113.11',
            'name' => 'OtherSeedbox',
        ]);
});

test('store creates a seedbox for the authenticated user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user, AuthGuard::API->value)->postJson('/api/user/seedboxes', [
        'ip'   => '203.0.113.12',
        'name' => 'WeeklyVpn',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.ip', '203.0.113.12')
        ->assertJsonPath('data.name', 'WeeklyVpn');

    $seedbox = Seedbox::query()->where('user_id', '=', $user->id)->sole();

    expect($seedbox->ip)->toBe('203.0.113.12')
        ->and($seedbox->name)->toBe('WeeklyVpn');
});

test('store rejects duplicate seedbox names and ips for the authenticated user', function (): void {
    $user = User::factory()->create();
    Seedbox::factory()->create([
        'ip'      => '203.0.113.13',
        'name'    => 'ExistingSeedbox',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user, AuthGuard::API->value)->postJson('/api/user/seedboxes', [
        'ip'   => '203.0.113.13',
        'name' => 'ExistingSeedbox',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['ip', 'name']);
});

test('destroy rejects seedboxes owned by another user', function (): void {
    $user = User::factory()->create();
    $otherSeedbox = Seedbox::factory()->create([
        'ip'   => '203.0.113.15',
        'name' => 'OtherVpn',
    ]);

    $this->actingAs($user, AuthGuard::API->value)
        ->deleteJson('/api/user/seedboxes/'.$otherSeedbox->id)
        ->assertForbidden();

    $this->assertModelExists($otherSeedbox);
});

test('destroy deletes the authenticated users seedbox', function (): void {
    $user = User::factory()->create();
    $seedbox = Seedbox::factory()->create([
        'ip'      => '203.0.113.14',
        'name'    => 'OldVpn',
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, AuthGuard::API->value)
        ->deleteJson('/api/user/seedboxes/'.$seedbox->id)
        ->assertOk()
        ->assertJsonPath('success', true);

    assertDatabaseMissing('seedboxes', [
        'id' => $seedbox->id,
    ]);
});
