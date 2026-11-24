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

use App\Models\Ticket;
use App\Models\TicketNote;
use App\Models\User;

test('store returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();
    $user = User::factory()->moderator()->create();
    $note = TicketNote::factory()->for($ticket)->for($user)->make();

    $this->actingAs($user)->post(route('tickets.note.store', [$ticket]), $note->toArray())
        ->assertRedirectToRoute('tickets.show', [$ticket])
        ->assertSessionHas('success', __('ticket.note-create-success'));

    $this->assertDatabaseHas('ticket_notes', $note->toArray());
});

test('destroy returns an ok response', function (): void {
    $ticket = Ticket::factory()->hasNotes(3)->create();
    $user = User::factory()->moderator()->create();

    $response = $this->actingAs($user)->delete(route('tickets.note.destroy', [$ticket]))
        ->assertRedirectToRoute('tickets.show', [$ticket])
        ->assertSessionHas('success', __('ticket.note-destroy-success'));

    $this->assertDatabaseMissing('ticket_notes', [
        'ticket_id' => $ticket->id,
    ]);
});
