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
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;

test('close returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($ticket->user)->post(route('tickets.close', [$ticket]))
        ->assertRedirectToRoute('tickets.index')
        ->assertSessionHas('success', __('ticket.closed-success'));
});

test('reopen returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($ticket->user)->post(route('tickets.reopen', [$ticket]))
        ->assertRedirectToRoute('tickets.show', [$ticket])
        ->assertSessionHas('success', __('ticket.reopen-success'));

    $this->assertDatabaseHas('tickets', [
        'id'        => $ticket->id,
        'closed_at' => null,
    ]);
});

test('create returns an ok response', function (): void {
    $ticketCategories = TicketCategory::factory()->times(3)->create();
    $ticketPriorities = TicketPriority::factory()->times(3)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tickets.create'))
        ->assertOk()
        ->assertViewIs('ticket.create')
        ->assertViewHas('categories')
        ->assertViewHas('priorities');
});

test('destroy returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();
    $user = User::factory()->moderator()->create();

    $response = $this->actingAs($user)->delete(route('tickets.destroy', [$ticket]))
        ->assertRedirectToRoute('tickets.index')
        ->assertSessionHas('success', __('ticket.deleted-success'));

    $this->assertModelMissing($ticket);
});

test('index returns an ok response', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tickets.index'))
        ->assertOk()
        ->assertViewIs('ticket.index');
});

test('show returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($ticket->user)->get(route('tickets.show', [$ticket]))
        ->assertOk()
        ->assertViewIs('ticket.show')
        ->assertViewHas('ticket', $ticket);
});

test('store returns an ok response', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->make();

    $response = $this->actingAs($user)->post(route('tickets.store'), $ticket->toArray())
        ->assertRedirect()
        ->assertSessionHas('success', __('ticket.created-success'));
});
