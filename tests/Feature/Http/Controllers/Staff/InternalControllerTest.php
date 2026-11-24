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

use App\Models\Internal;

test('create returns an ok response', function (): void {
    $this->get(route('staff.internals.create'))
        ->assertOk()
        ->assertViewIs('Staff.internals.create');
});

test('destroy returns an ok response', function (): void {
    $internal = Internal::factory()->create();

    $this->delete(route('staff.internals.destroy', [$internal]))
        ->assertRedirectToRoute('staff.internals.index')
        ->assertSessionHas('success', 'Group has been removed.');
    $this->assertModelMissing($internal);
});

test('edit returns an ok response', function (): void {
    $internal = Internal::factory()->create();

    $this->get(route('staff.internals.edit', [$internal]))
        ->assertOk()
        ->assertViewIs('Staff.internals.edit')
        ->assertViewHas('internal', $internal);
});

test('index returns an ok response', function (): void {
    $internals = Internal::factory()->times(3)->create();

    $this->get(route('staff.internals.index'))
        ->assertOk()
        ->assertViewIs('Staff.internals.index');
});

test('store returns an ok response', function (): void {
    $internal = Internal::factory()->make();

    $this->post(route('staff.internals.store'), $internal->toArray())
        ->assertRedirectToRoute('staff.internals.index')
        ->assertSessionHas('success', 'New internal group added!');

    $this->assertDatabaseHas('internals', $internal->toArray());
});

test('update returns an ok response', function (): void {
    $internal = Internal::factory()->create();
    $newInternal = Internal::factory()->make();

    $this->patch(route('staff.internals.update', [$internal]), $newInternal->toArray())
        ->assertRedirectToRoute('staff.internals.index')
        ->assertSessionHas('success', 'Internal group was updated successfully!');

    $this->assertDatabaseHas('internals', [
        'id' => $internal->id,
        ...$newInternal->toArray(),
    ]);
});
