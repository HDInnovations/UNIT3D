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

use App\Models\BonEarning;

test('create returns an ok response', function (): void {
    $this->get(route('staff.bon_earnings.create'))
        ->assertOk()
        ->assertViewIs('Staff.bon-earning.create');
});

test('destroy returns an ok response', function (): void {
    $bonEarning = BonEarning::factory()->create();

    $this->delete(route('staff.bon_earnings.destroy', [$bonEarning]))
        ->assertRedirect(route('staff.bon_earnings.index'))
        ->assertSessionHas('success', 'Bon earning successfully deleted');

    $this->assertModelMissing($bonEarning);
});

test('edit returns an ok response', function (): void {
    $bonEarning = BonEarning::factory()->create();

    $this->get(route('staff.bon_earnings.edit', [$bonEarning]))
        ->assertOk()
        ->assertViewIs('Staff.bon-earning.edit')
        ->assertViewHas('bonEarning');
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.bon_earnings.index'))
        ->assertOk()
        ->assertViewIs('Staff.bon-earning.index')
        ->assertViewHas('bonEarnings');
});

test('store returns an ok response', function (): void {
    $bonEarning = BonEarning::factory()->make();

    $this->post(route('staff.bon_earnings.store'), [
        'bon_earning' => $bonEarning->toArray(),
    ])
        ->assertRedirect(route('staff.bon_earnings.index'))
        ->assertSessionHas('success', 'Bon earning successfully added');
});

test('update returns an ok response', function (): void {
    $bonEarning = BonEarning::factory()->create();

    $this->patch(route('staff.bon_earnings.update', [$bonEarning]), [
        'bon_earning' => $bonEarning->toArray(),
    ])
        ->assertRedirect(route('staff.bon_earnings.index'))
        ->assertSessionHas('success', 'Bon earning successfully modified');
});
