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

use App\Models\TicketPriority;

test('create returns an ok response', function (): void {
    $this->get(route('staff.ticket_priorities.create'))
        ->assertOk()
        ->assertViewIs('Staff.ticket-priority.create');
});

test('destroy returns an ok response', function (): void {
    $ticketPriority = TicketPriority::factory()->create();

    $this->delete(route('staff.ticket_priorities.destroy', [$ticketPriority]))
        ->assertRedirectToRoute('staff.ticket_priorities.index')
        ->assertSessionHas('success', 'Ticket priority successfully deleted');
    $this->assertModelMissing($ticketPriority);
});

test('edit returns an ok response', function (): void {
    $ticketPriority = TicketPriority::factory()->create();

    $this->get(route('staff.ticket_priorities.edit', [$ticketPriority]))
        ->assertOk()
        ->assertViewIs('Staff.ticket-priority.edit')
        ->assertViewHas('ticketPriority', $ticketPriority);
});

test('index returns an ok response', function (): void {
    $ticketPriorities = TicketPriority::factory()->times(3)->create();

    $this->get(route('staff.ticket_priorities.index'))
        ->assertOk()
        ->assertViewIs('Staff.ticket-priority.index')
        ->assertViewHas('ticketPriorities');
});

test('store returns an ok response', function (): void {
    $ticketPriority = TicketPriority::factory()->make();

    $this->post(route('staff.ticket_priorities.store'), $ticketPriority->toArray())
        ->assertRedirectToRoute('staff.ticket_priorities.index')
        ->assertSessionHas('success', 'Ticket priority successfully added');

    $this->assertDatabaseHas('ticket_priorities', $ticketPriority->toArray());
});

test('update returns an ok response', function (): void {
    $ticketPriority = TicketPriority::factory()->create();
    $newTicketPriority = TicketPriority::factory()->make();

    $this->patch(route('staff.ticket_priorities.update', [$ticketPriority]), $newTicketPriority->toArray())
        ->assertRedirectToRoute('staff.ticket_priorities.index')
        ->assertSessionHas('success', 'Ticket priority successfully modified');

    $this->assertDatabaseHas('ticket_priorities', [
        'id' => $ticketPriority->id,
        ...$newTicketPriority->toArray(),
    ]);
});
