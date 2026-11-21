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
use App\Models\User;

test('store returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();
    $user = User::factory()->moderator()->create();
    $assigned = User::factory()->moderator()->create();

    $this->actingAs($user)->post(route('tickets.assignee.store', [$ticket]), ['staff_id' => $assigned->id])
        ->assertRedirectToRoute('tickets.show', [$ticket])
        ->assertSessionHas('success', __('ticket.assigned-success'));

    $this->assertDatabaseHas('tickets', [
        'id'         => $ticket->id,
        'staff_id'   => $assigned->id,
        'staff_read' => false,
    ]);
});

test('destroy returns an ok response', function (): void {
    $ticket = Ticket::factory()->create();
    $user = User::factory()->moderator()->create();

    $response = $this->actingAs($user)->delete(route('tickets.assignee.destroy', [$ticket]))
        ->assertRedirectToRoute('tickets.show', [$ticket])
        ->assertSessionHas('success', __('ticket.unassigned-success'));

    $this->assertDatabaseHas('tickets', [
        'id'       => $ticket->id,
        'staff_id' => null,
    ]);
});
