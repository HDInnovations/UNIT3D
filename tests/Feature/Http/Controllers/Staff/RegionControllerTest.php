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

use App\Models\Region;

test('create returns an ok response', function (): void {
    $this->get(route('staff.regions.create'))
        ->assertOk()
        ->assertViewIs('Staff.region.create');
});

test('destroy returns an ok response', function (): void {
    $region = Region::factory()->create();
    $replacement = Region::factory()->create();

    $this->delete(route('staff.regions.destroy', [$region]), [
        'region_id' => $replacement->id,
    ])
        ->assertRedirectToRoute('staff.regions.index')
        ->assertSessionHas('success', 'Region successfully deleted');

    $this->assertModelMissing($region);
});

test('edit returns an ok response', function (): void {
    $region = Region::factory()->create();

    $this->get(route('staff.regions.edit', [$region]))
        ->assertOk()
        ->assertViewIs('Staff.region.edit')
        ->assertViewHas('region', $region);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.regions.index'))
        ->assertOk()
        ->assertViewIs('Staff.region.index');
});

test('store returns an ok response', function (): void {
    $region = Region::factory()->make();

    $this->post(route('staff.regions.store'), $region->toArray())
        ->assertRedirectToRoute('staff.regions.index')
        ->assertSessionHas('success', 'Region successfully added');

    $this->assertDatabaseHas('regions', $region->toArray());
});

test('update returns an ok response', function (): void {
    $region = Region::factory()->create();
    $newRegion = Region::factory()->make();

    $this->patch(route('staff.regions.update', [$region]), $newRegion->toArray())
        ->assertRedirectToRoute('staff.regions.index')
        ->assertSessionHas('success', 'Region successfully modified');

    $this->assertDatabaseHas('regions', [
        'id' => $region->id,
        ...$newRegion->toArray(),
    ]);
});
