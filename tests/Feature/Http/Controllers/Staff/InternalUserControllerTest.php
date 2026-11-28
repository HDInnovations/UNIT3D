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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\InternalUser;

test('destroy returns an ok response', function (): void {
    $internalUser = InternalUser::factory()->create();

    $this->delete(route('staff.internal_users.destroy', [$internalUser]))
        ->assertRedirectToRoute('staff.internals.edit', [$internalUser->internal])
        ->assertSessionHas('success', 'User removed from group.');
    $this->assertModelMissing($internalUser);
});

test('store returns an ok response', function (): void {
    $internalUser = InternalUser::factory()->make();

    $this->post(route('staff.internal_users.store'), [
        'username'    => $internalUser->user->username,
        'internal_id' => $internalUser->internal_id,
        'position'    => $internalUser->position,
    ])
        ->assertRedirectToRoute('staff.internals.edit', [$internalUser->internal])
        ->assertSessionHas('success', 'User added to group.');

    $this->assertDatabaseHas('internal_user', [
        'user_id'     => $internalUser->user_id,
        'internal_id' => $internalUser->internal_id,
        'position'    => $internalUser->position,
    ]);
});

test('update returns an ok response', function (): void {
    $internalUser = InternalUser::factory()->create();
    $newInternalUser = InternalUser::factory()->make();

    $this->patch(route('staff.internal_users.update', [$internalUser]), [
        'position' => $newInternalUser->position,
    ])
        ->assertRedirectToRoute('staff.internals.edit', [$internalUser->internal])
        ->assertSessionHas('success', 'User updated.');

    $this->assertDatabaseHas('internal_user', [
        'id'       => $internalUser->id,
        'position' => $newInternalUser->position,
    ]);
});
