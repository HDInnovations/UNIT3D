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

use App\Models\Group;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAutoCloseReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('it reminds the ticket creator after a stale staff reply', function (): void {
    Notification::fake();

    config([
        'ticket.auto_close_reminder_days' => 3,
        'ticket.auto_close_grace_hours'   => 24,
    ]);

    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => false])->id,
    ]);
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);
    $ticket = Ticket::factory()->create([
        'user_id'     => $user->id,
        'staff_id'    => $staff->id,
        'user_read'   => true,
        'closed_at'   => null,
        'reminded_at' => null,
        'deleted_at'  => null,
    ]);
    $ticket->comments()->create([
        'content'    => 'Can you confirm this is still an issue?',
        'user_id'    => $staff->id,
        'anon'       => false,
        'created_at' => now()->subDays(4),
        'updated_at' => now()->subDays(4),
    ]);

    $this->artisan('auto:close_inactive_tickets')
        ->assertExitCode(0)
        ->run();

    $ticket->refresh();

    expect($ticket->closed_at)->toBeNull()
        ->and($ticket->reminded_at)->not->toBeNull()
        ->and($ticket->user_read)->toBeFalse();

    Notification::assertSentTo(
        $user,
        TicketAutoCloseReminder::class,
        fn (TicketAutoCloseReminder $notification) => $notification->ticket->is($ticket) && $notification->graceHours === 24
    );
});

test('it closes reminded tickets after the grace window when the latest reply is still staff', function (): void {
    Notification::fake();

    config([
        'ticket.auto_close_reminder_days' => 3,
        'ticket.auto_close_grace_hours'   => 24,
    ]);

    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => false])->id,
    ]);
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);
    $ticket = Ticket::factory()->create([
        'user_id'     => $user->id,
        'staff_id'    => $staff->id,
        'closed_at'   => null,
        'reminded_at' => now()->subHours(25),
        'deleted_at'  => null,
    ]);
    $ticket->comments()->create([
        'content'    => 'Following up before closure.',
        'user_id'    => $staff->id,
        'anon'       => false,
        'created_at' => now()->subDays(4),
        'updated_at' => now()->subDays(4),
    ]);

    $this->artisan('auto:close_inactive_tickets')
        ->assertExitCode(0)
        ->run();

    expect($ticket->refresh()->closed_at)->not->toBeNull();

    Notification::assertNothingSent();
});

test('it does not close a reminded ticket after the user replies', function (): void {
    config([
        'ticket.auto_close_reminder_days' => 3,
        'ticket.auto_close_grace_hours'   => 24,
    ]);

    $user = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => false])->id,
    ]);
    $staff = User::factory()->create([
        'group_id' => Group::factory()->create(['is_modo' => true])->id,
    ]);
    $ticket = Ticket::factory()->create([
        'user_id'     => $user->id,
        'staff_id'    => $staff->id,
        'closed_at'   => null,
        'reminded_at' => now()->subHours(25),
        'deleted_at'  => null,
    ]);
    $ticket->comments()->create([
        'content'    => 'Following up before closure.',
        'user_id'    => $staff->id,
        'anon'       => false,
        'created_at' => now()->subDays(4),
        'updated_at' => now()->subDays(4),
    ]);
    $ticket->comments()->create([
        'content'    => 'I still need help.',
        'user_id'    => $user->id,
        'anon'       => false,
        'created_at' => now()->subHours(2),
        'updated_at' => now()->subHours(2),
    ]);

    $this->artisan('auto:close_inactive_tickets')
        ->assertExitCode(0)
        ->run();

    expect($ticket->refresh()->closed_at)->toBeNull();
});
