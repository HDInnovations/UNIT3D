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

namespace Tests\Feature\Http\Controllers\Staff;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

beforeEach(function (): void {
    // Create a staff user with all permissions
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);

    // Create regular user groups
    $this->group1 = Group::factory()->create(['position' => 2]);
    $this->group2 = Group::factory()->create(['position' => 3]);

    // Create regular users
    $this->regularUser1 = User::factory()->create(['group_id' => $this->group1->id]);
    $this->regularUser2 = User::factory()->create(['group_id' => $this->group2->id]);
});

test('staff can view mass private message creation page', function (): void {
    $response = $this->actingAs($this->staffUser)
        ->get(route('staff.mass_private_message.create'));

    $response->assertOk()
        ->assertViewIs('Staff.mass_private_message.create')
        ->assertViewHas('groups');
});

test('staff can send mass private messages', function (): void {
    Bus::fake();

    $response = $this->actingAs($this->staffUser)
        ->post(route('staff.mass_private_message.store'), [
            'groups'  => [$this->group1->id, $this->group2->id],
            'subject' => 'Test Mass PM Subject',
            'message' => 'Test Mass PM Message Content'
        ]);

    $response->assertRedirect(route('staff.dashboard.index'))
        ->assertSessionHas('success');
});

test('non-staff cannot access mass private message pages', function (): void {
    $regularUser = User::factory()->create();

    $this->actingAs($regularUser)
        ->get(route('staff.mass_private_message.create'))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->post(route('staff.mass_private_message.store'), [
            'groups'  => [$this->group1->id, $this->group2->id],
            'subject' => 'Test Mass PM Subject',
            'message' => 'Test Mass PM Message Content'
        ])
        ->assertForbidden();
});

test('validation rules are enforced when sending mass private messages', function (): void {
    $response = $this->actingAs($this->staffUser)
        ->post(route('staff.mass_private_message.store'), [
            'groups'  => [],
            'subject' => '',
            'message' => ''
        ]);

    $response->assertSessionHasErrors(['groups', 'subject', 'message']);
});
