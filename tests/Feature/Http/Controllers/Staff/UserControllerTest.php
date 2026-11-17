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

use App\Models\Group;
use App\Models\Internal;
use App\Models\User;

test('edit returns an ok response', function (): void {
    $user = User::factory()->create();
    $groups = Group::factory()->times(3)->create();
    $internals = Internal::factory()->times(3)->create();

    $response = $this->get(route('staff.users.edit', [$user]))
        ->assertOk()
        ->assertViewIs('Staff.user.edit')
        ->assertViewHas('user', $user)
        ->assertViewHas('groups')
        ->assertViewHas('internals');
});

test('index returns an ok response', function (): void {
    $response = $this->get(route('staff.users.index'))
        ->assertOk()
        ->assertViewIs('Staff.user.index');
});

test('permissions returns an ok response', function (): void {
    $user = User::factory()->create();

    $this->patch(route('staff.users.update_permissions', [$user]), [
        'can_chat'    => false,
        'can_comment' => false,
        'can_invite'  => false,
        'can_request' => false,
        'can_upload'  => false,
        'is_donor'    => false,
        'is_lifetime' => false,
    ])
        ->assertRedirectToRoute('users.show', $user);

    $this->assertDatabaseHas('users', [
        'id'          => $user->id,
        'can_chat'    => false,
        'can_comment' => false,
        'can_invite'  => false,
        'can_request' => false,
        'can_upload'  => false,
        'is_donor'    => false,
        'is_lifetime' => false,
    ]);
});

test('update returns an ok response', function (): void {
    $user = User::factory()->create();
    $newUser = User::factory()->make()->only([
        'username',
        'uploaded',
        'downloaded',
        'title',
        'about',
        'group_id',
        'seedbonus',
        'invites',
        'fl_tokens',
    ]);

    $response = $this->patch(route('staff.users.update', [$user]), $newUser)
        ->assertRedirectToRoute('users.show', $newUser['username'])
        ->assertSessionHas('success', 'Account was updated successfully!');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        ...$newUser,
    ]);
});
