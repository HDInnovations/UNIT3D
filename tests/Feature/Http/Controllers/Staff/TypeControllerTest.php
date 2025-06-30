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

use App\Models\Type;

use function Pest\Laravel\assertDatabaseHas;

test('create returns an ok response', function (): void {
    $this->get(route('staff.types.create'))
        ->assertOk()
        ->assertViewIs('Staff.type.create');
});

test('destroy returns an ok response', function (): void {
    $type = Type::factory()->create();

    $this->delete(route('staff.types.destroy', [$type]))
        ->assertRedirect(route('staff.types.index'))
        ->assertSessionHasNoErrors();

    $this->assertModelMissing($type);
});

test('edit returns an ok response', function (): void {
    $type = Type::factory()->create();

    $this->get(route('staff.types.edit', [$type]))
        ->assertOk()
        ->assertViewIs('Staff.type.edit')
        ->assertViewHas('type', $type);
});

test('index returns an ok response', function (): void {
    Type::factory()->times(3)->create();

    $this->get(route('staff.types.index'))
        ->assertOk()
        ->assertViewIs('Staff.type.index')
        ->assertViewHas('types');
});

test('store returns an ok response', function (): void {
    $type = Type::factory()->make();

    $response = $this->post(route('staff.types.store'), [
        'name'     => $type->name,
        'position' => $type->position,
    ]);

    $response->assertRedirect(route('staff.types.index'))->assertSessionHasNoErrors();

    assertDatabaseHas('types', [
        'name'     => $type->name,
        'position' => $type->position,
    ]);
});

test('update returns an ok response', function (): void {
    $type = Type::factory()->create();

    $response = $this->patch(route('staff.types.update', ['type' => $type]), [
        'name'     => 'test_name',
        'position' => 999,
    ]);

    $response->assertRedirect(route('staff.types.index'))->assertSessionHasNoErrors();

    assertDatabaseHas('types', [
        'name'     => 'test_name',
        'position' => 999,
    ]);
});
