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

use App\Models\Resolution;

test('create returns an ok response', function (): void {
    $response = $this->get(route('staff.resolutions.create'))
        ->assertOk()
        ->assertViewIs('Staff.resolution.create');
});

test('destroy returns an ok response', function (): void {
    $resolution = Resolution::factory()->create();

    $this->delete(route('staff.resolutions.destroy', [$resolution]))
        ->assertRedirectToRoute('staff.resolutions.index');
    $this->assertModelMissing($resolution);
});

test('edit returns an ok response', function (): void {
    $resolution = Resolution::factory()->create();

    $this->get(route('staff.resolutions.edit', [$resolution]))
        ->assertOk()
        ->assertViewIs('Staff.resolution.edit')
        ->assertViewHas('resolution', $resolution);
});

test('index returns an ok response', function (): void {
    $resolutions = Resolution::factory()->times(3)->create();

    $this->get(route('staff.resolutions.index'))
        ->assertOk()
        ->assertViewIs('Staff.resolution.index')
        ->assertViewHas('resolutions');
});

test('store returns an ok response', function (): void {
    $resolution = Resolution::factory()->make();

    $this->post(route('staff.resolutions.store'), $resolution->toArray())
        ->assertRedirectToRoute('staff.resolutions.index')
        ->assertSessionHas('success', 'Resolution successfully added');

    $this->assertDatabaseHas('resolutions', $resolution->toArray());
});

test('update returns an ok response', function (): void {
    $resolution = Resolution::factory()->create();
    $newResolution = Resolution::factory()->make();

    $this->patch(route('staff.resolutions.update', [$resolution]), $newResolution->toArray())
        ->assertRedirectToRoute('staff.resolutions.index')
        ->assertSessionHas('success', 'Resolution successfully modified');

    $this->assertDatabaseHas('resolutions', [
        'id' => $resolution->id,
        ...$newResolution->toArray(),
    ]);
});
