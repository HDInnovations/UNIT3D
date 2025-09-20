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

use App\Http\Controllers\Staff\RegionController;
use App\Http\Requests\Staff\DestroyRegionRequest;
use App\Http\Requests\Staff\StoreRegionRequest;
use App\Http\Requests\Staff\UpdateRegionRequest;
use App\Models\Group;
use App\Models\Region;
use App\Models\User;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('create returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.regions.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.region.create');
});

test('destroy validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        RegionController::class,
        'destroy',
        DestroyRegionRequest::class
    );
});

test('destroy returns an ok response', function (): void {
    $this->markTestIncomplete('Test Incomplete.');

    $region = Region::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.regions.destroy', [$region]));
    $response->assertRedirect(route('staff.regions.index'))->assertSessionHas('success', 'Region successfully deleted');

    $this->assertModelMissing($region);
});

test('edit returns an ok response', function (): void {
    $region = Region::factory()->create();

    $response = $this->actingAs($this->staffUser)->get(route('staff.regions.edit', [$region]));
    $response->assertOk();
    $response->assertViewIs('Staff.region.edit');
    $response->assertViewHas('region', $region);
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.regions.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.region.index');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        RegionController::class,
        'store',
        StoreRegionRequest::class
    );
});

test('store returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->post(route('staff.regions.store'), [
        'id'       => 1,
        'name'     => 'AFG',
        'position' => 0,
    ]);
    $response->assertRedirect(route('staff.regions.index'))->assertSessionHas('success', 'Region successfully added');
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        RegionController::class,
        'update',
        UpdateRegionRequest::class
    );
});

test('update returns an ok response', function (): void {
    $region = Region::factory()->create();

    $response = $this->actingAs($this->staffUser)->patch(route('staff.regions.update', [$region]), [
        'name'     => $region->name,
        'position' => 1,
    ]);
    $response->assertRedirect(route('staff.regions.index'))->assertSessionHas('success', 'Region successfully modified');
});
