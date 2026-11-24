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

use App\Models\ChatStatus;

use function Pest\Laravel\assertDatabaseHas;

test('create returns an ok response', function (): void {
    $this->get(route('staff.statuses.create'))
        ->assertOk()
        ->assertViewIs('Staff.chat.status.create');
});

test('destroy returns an ok response', function (): void {
    $chatStatus = ChatStatus::factory()->create();

    $this->delete(route('staff.statuses.destroy', [$chatStatus]))
        ->assertRedirect(route('staff.statuses.index'))
        ->assertSessionHasNoErrors();

    $this->assertModelMissing($chatStatus);
});

test('edit returns an ok response', function (): void {
    $chatStatus = ChatStatus::factory()->create();

    $this->get(route('staff.statuses.edit', [$chatStatus]))
        ->assertOk()
        ->assertViewIs('Staff.chat.status.edit')
        ->assertViewHas('chatstatus', $chatStatus);
});

test('index returns an ok response', function (): void {
    ChatStatus::factory()->times(3)->create();

    $this->get(route('staff.statuses.index'))
        ->assertOk()
        ->assertViewIs('Staff.chat.status.index')
        ->assertViewHas('chatstatuses');
});

test('store returns an ok response', function (): void {
    $chatStatus = ChatStatus::factory()->make();

    $this->post(route('staff.statuses.store'), [
        'name'  => $chatStatus->name,
        'color' => $chatStatus->color,
        'icon'  => $chatStatus->icon,
    ])
        ->assertRedirect(route('staff.statuses.index'))
        ->assertSessionHasNoErrors();

    assertDatabaseHas('chat_statuses', [
        'name'  => $chatStatus->name,
        'color' => $chatStatus->color,
        'icon'  => $chatStatus->icon,
    ]);
});

test('update returns an ok response', function (): void {
    $chatStatus = ChatStatus::factory()->create();

    $this->post(route('staff.statuses.update', [$chatStatus]), [
        'name'  => 'new name',
        'color' => 'black',
        'icon'  => 'cog',
    ])
        ->assertRedirect(route('staff.statuses.index'));

    assertDatabaseHas('chat_statuses', [
        'name'  => 'new name',
        'color' => 'black',
        'icon'  => 'cog',
    ]);
});
