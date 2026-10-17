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
use Database\Seeders\UserSeeder;

/**
 * @see App\Console\Commands\AutoCloseInactiveTickets
 */
beforeEach(function (): void {
    $this->seed(UserSeeder::class);
});

it('runs successfully with no tickets', function (): void {
    $this->artisan('tickets:auto-close-inactive')
        ->assertExitCode(0)
        ->run();
});

it('pings a ticket when staff replied more than 3 days ago and no warning exists yet', function (): void {
    $user  = User::factory()->create();
    $staff = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id'   => $user->id,
        'staff_id'  => $staff->id,
        'closed_at' => null,
    ]);

    $ticket->comments()->create([
        'user_id'          => $staff->id,
        'content'          => 'Please send more details.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDays(3)->subHour(),
    ]);

    $this->artisan('tickets:auto-close-inactive')
        ->assertExitCode(0)
        ->run();

    expect($ticket->fresh()->closed_at)->toBeNull();

    $systemComment = $ticket->comments()
        ->where('user_id', User::SYSTEM_USER_ID)
        ->first();

    expect($systemComment)->not->toBeNull();
    expect($systemComment->content)->toContain('automatically closed in 24 hours');
});

it('closes a ticket when a warning was already sent and staff replied more than 4 days ago', function (): void {
    $user  = User::factory()->create();
    $staff = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id'   => $user->id,
        'staff_id'  => $staff->id,
        'closed_at' => null,
    ]);

    $staffReplyAt = now()->subDays(4)->subHour();

    $ticket->comments()->create([
        'user_id'          => $staff->id,
        'content'          => 'Waiting on you.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => $staffReplyAt,
        'updated_at'       => $staffReplyAt,
    ]);

    $ticket->comments()->create([
        'user_id'          => User::SYSTEM_USER_ID,
        'content'          => 'This ticket will be automatically closed in 24 hours if there is no further response from you.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDays(3)->subHour(),
        'updated_at'       => now()->subDays(3)->subHour(),
    ]);

    $this->artisan('tickets:auto-close-inactive')
        ->assertExitCode(0)
        ->run();

    expect($ticket->fresh()->closed_at)->not->toBeNull();

    $closureComment = $ticket->comments()
        ->where('user_id', User::SYSTEM_USER_ID)
        ->where('content', 'like', '%automatically closed due to prolonged%')
        ->first();

    expect($closureComment)->not->toBeNull();
});

it('does not close or ping a ticket when the user has replied after the staff', function (): void {
    $user  = User::factory()->create();
    $staff = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id'   => $user->id,
        'staff_id'  => $staff->id,
        'closed_at' => null,
    ]);

    $ticket->comments()->create([
        'user_id'          => $staff->id,
        'content'          => 'Staff reply.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDays(5),
    ]);

    $ticket->comments()->create([
        'user_id'          => $user->id,
        'content'          => 'User follow-up.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDay(),
    ]);

    $this->artisan('tickets:auto-close-inactive')
        ->assertExitCode(0)
        ->run();

    expect($ticket->fresh()->closed_at)->toBeNull();

    $systemComment = $ticket->comments()
        ->where('user_id', User::SYSTEM_USER_ID)
        ->first();

    expect($systemComment)->toBeNull();
});

it('does not modify any records when the dry-run flag is set', function (): void {
    $user  = User::factory()->create();
    $staff = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id'   => $user->id,
        'staff_id'  => $staff->id,
        'closed_at' => null,
    ]);

    $ticket->comments()->create([
        'user_id'          => $staff->id,
        'content'          => 'Waiting on you.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDays(4)->subHour(),
    ]);

    $this->artisan('tickets:auto-close-inactive --dry-run')
        ->assertExitCode(0)
        ->run();

    expect($ticket->fresh()->closed_at)->toBeNull();

    $systemComment = $ticket->comments()
        ->where('user_id', User::SYSTEM_USER_ID)
        ->first();

    expect($systemComment)->toBeNull();
});

it('skips tickets that are already closed', function (): void {
    $user  = User::factory()->create();
    $staff = User::factory()->create();

    $ticket = Ticket::factory()->create([
        'user_id'   => $user->id,
        'staff_id'  => $staff->id,
        'closed_at' => now()->subWeek(),
    ]);

    $ticket->comments()->create([
        'user_id'          => $staff->id,
        'content'          => 'Old reply.',
        'anon'             => false,
        'commentable_type' => Ticket::class,
        'commentable_id'   => $ticket->id,
        'created_at'       => now()->subDays(10),
    ]);

    $commentCountBefore = $ticket->comments()->count();

    $this->artisan('tickets:auto-close-inactive')
        ->assertExitCode(0)
        ->run();

    expect($ticket->comments()->count())->toBe($commentCountBefore);
});
