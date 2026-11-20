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

use App\Models\Distributor;

test('create returns an ok response', function (): void {
    $this->get(route('staff.distributors.create'))
        ->assertOk()
        ->assertViewIs('Staff.distributor.create');
});

test('delete returns an ok response', function (): void {
    $distributors = Distributor::factory()->times(3)->create();

    $this->get(route('staff.distributors.delete', [$distributors->last()]))
        ->assertOk()
        ->assertViewIs('Staff.distributor.delete')
        ->assertViewHas('distributors');
});

test('destroy returns an ok response', function (): void {
    $distributor = Distributor::factory()->create();
    $replacement = Distributor::factory()->create();

    $this->delete(route('staff.distributors.destroy', [$distributor]), [
        'distributor_id' => $replacement->id,
    ])
        ->assertRedirect(route('staff.distributors.index'));
    $this->assertModelMissing($distributor);
});

test('edit returns an ok response', function (): void {
    $distributor = Distributor::factory()->create();

    $this->get(route('staff.distributors.edit', [$distributor]))
        ->assertOk()
        ->assertViewIs('Staff.distributor.edit')
        ->assertViewHas('distributor', $distributor);
});

test('index returns an ok response', function (): void {
    $distributors = Distributor::factory()->times(3)->create();

    $this->get(route('staff.distributors.index'))
        ->assertOk()
        ->assertViewIs('Staff.distributor.index')
        ->assertViewHas('distributors');
});

test('store returns an ok response', function (): void {
    $distributor = Distributor::factory()->make();

    $this->post(route('staff.distributors.store'), [
        'name' => $distributor->name,
    ])
        ->assertRedirectToRoute('staff.distributors.index');

    $this->assertDatabaseHas('distributors', [
        'name' => $distributor->name,
    ]);
});

test('update returns an ok response', function (): void {
    $distributor = Distributor::factory()->create();
    $newDistributor = Distributor::factory()->make();

    $this->patch(route('staff.distributors.update', [$distributor]), [
        'name' => $newDistributor->name,
    ])
        ->assertRedirectToRoute('staff.distributors.index');
    $this->assertDatabaseHas('distributors', [
        'id'   => $distributor->id,
        'name' => $newDistributor->name,
    ]);
});
