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

use App\Models\Giveaway;

test('create returns an ok response', function (): void {
    $this->get(route('staff.giveaways.create'))
        ->assertOk()
        ->assertViewIs('Staff.giveaway.create');
});

test('destroy returns an ok response', function (): void {
    $giveaway = Giveaway::factory()->create();

    $this->delete(route('staff.giveaways.destroy', [$giveaway]))
        ->assertRedirectToRoute('staff.giveaways.index');

    $this->assertModelMissing($giveaway);
});

test('edit returns an ok response', function (): void {
    $giveaway = Giveaway::factory()->create();

    $this->get(route('staff.giveaways.edit', [$giveaway]))
        ->assertOk()
        ->assertViewIs('Staff.giveaway.edit')
        ->assertViewHas('giveaway', $giveaway);
});

test('store returns an ok response', function (): void {
    $giveaway = Giveaway::factory()->make();

    $this->post(route('staff.giveaways.store'), $giveaway->toArray())
        ->assertRedirectToRoute('staff.giveaways.index');

    $this->assertDatabaseHas('giveaways', $giveaway->toArray());
});

test('update returns an ok response', function (): void {
    $giveaway = Giveaway::factory()->create();
    $newGiveaway = Giveaway::factory()->make();

    $this->patch(route('staff.giveaways.update', [$giveaway]), $newGiveaway->toArray())
        ->assertRedirectToRoute('staff.giveaways.index');

    $this->assertDatabaseHas('giveaways', $newGiveaway->toArray());
});
