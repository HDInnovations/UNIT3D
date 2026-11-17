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

use App\Models\TicketCategory;

test('create returns an ok response', function (): void {
    $this->get(route('staff.ticket_categories.create'))
        ->assertOk()
        ->assertViewIs('Staff.ticket-category.create');
});

test('destroy returns an ok response', function (): void {
    $ticketCategory = TicketCategory::factory()->create();

    $this->delete(route('staff.ticket_categories.destroy', [$ticketCategory]))
        ->assertRedirectToRoute('staff.ticket_categories.index')
        ->assertSessionHas('success', 'Ticket category successfully deleted');
    $this->assertModelMissing($ticketCategory);
});

test('edit returns an ok response', function (): void {
    $ticketCategory = TicketCategory::factory()->create();

    $this->get(route('staff.ticket_categories.edit', [$ticketCategory]))
        ->assertOk()
        ->assertViewIs('Staff.ticket-category.edit')
        ->assertViewHas('ticketCategory', $ticketCategory);
});

test('index returns an ok response', function (): void {
    $ticketCategories = TicketCategory::factory()->times(3)->create();

    $this->get(route('staff.ticket_categories.index'))
        ->assertOk()
        ->assertViewIs('Staff.ticket-category.index')
        ->assertViewHas('ticketCategories');
});

test('store returns an ok response', function (): void {
    $ticketCategory = TicketCategory::factory()->make();

    $this->post(route('staff.ticket_categories.store'), $ticketCategory->toArray())
        ->assertRedirectToRoute('staff.ticket_categories.index')
        ->assertSessionHas('success', 'Ticket category successfully added');

    $this->assertDatabaseHas('ticket_categories', $ticketCategory->toArray());
});

test('update returns an ok response', function (): void {
    $ticketCategory = TicketCategory::factory()->create();
    $newTicketCategory = TicketCategory::factory()->make();

    $this->patch(route('staff.ticket_categories.update', [$ticketCategory]), $newTicketCategory->toArray())
        ->assertRedirectToRoute('staff.ticket_categories.index')
        ->assertSessionHas('success', 'Ticket category successfully modified');

    $this->assertDatabaseHas('ticket_categories', [
        'id' => $ticketCategory->id,
        ...$newTicketCategory->toArray(),
    ]);
});
