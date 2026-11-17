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

use App\Models\Bot;
use App\Models\Group;
use App\Models\Option;
use App\Models\Poll;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('store a poll returns an ok response', function (): void {
    // Poll chat announcements needs a system user, a bot and a chatroom
    $this->seed(UserSeeder::class);
    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);

    $pollTitle = 'Test Poll Title';
    $pollOption1 = 'Option 1';
    $pollOption2 = 'Option 2';

    $response = $this->post(route('staff.polls.store'), [
        'title'   => $pollTitle,
        'options' => [
            ['id' => 0, 'name' => $pollOption1],
            ['id' => 1, 'name' => $pollOption2],
        ],
        'multiple_choice' => 0,
    ]);

    $response->assertRedirectToRoute('staff.polls.index');
    $this->assertDatabaseHas('polls', [
        'title'      => $pollTitle,
        'user_id'    => auth()->id(),
        'expires_at' => null,
    ]);
    $this->assertDatabaseHas('options', [
        'name' => $pollOption1,
    ]);
    $this->assertDatabaseHas('options', [
        'name' => $pollOption2,
    ]);
});

test('store a poll with expiration date returns an ok response', function (): void {
    // Poll chat announcements needs a system user, a bot and a chatroom
    $this->seed(UserSeeder::class);
    $bot = Bot::factory()->create([
        'command' => 'Systembot',
    ]);

    $pollTitle = 'Test Poll Title';
    $pollUntil = now()->addDays(1);
    $pollOption1 = 'Option 1';
    $pollOption2 = 'Option 2';

    $response = $this->post(route('staff.polls.store'), [
        'title'      => $pollTitle,
        'expires_at' => $pollUntil,
        'options'    => [
            ['id' => 0, 'name' => $pollOption1],
            ['id' => 1, 'name' => $pollOption2],
        ],
        'multiple_choice' => 0,
    ]);

    $this->assertDatabaseHas('polls', [
        'title'      => $pollTitle,
        'user_id'    => auth()->id(),
        'expires_at' => $pollUntil,
    ]);
    $this->assertDatabaseHas('options', [
        'name' => $pollOption1,
    ]);
    $this->assertDatabaseHas('options', [
        'name' => $pollOption2,
    ]);
});

test('destroy returns an ok response', function (): void {
    $poll = Poll::factory()->create();

    $this->delete(route('staff.polls.destroy', [$poll]))
        ->assertRedirectToRoute('staff.polls.index')
        ->assertSessionHas('success', 'Poll has successfully been deleted');

    $this->assertModelMissing($poll);
});

test('edit returns an ok response', function (): void {
    $poll = Poll::factory()->create();

    $response = $this->get(route('staff.polls.edit', [$poll]))
        ->assertOk()
        ->assertViewIs('Staff.poll.edit')
        ->assertViewHas('poll', $poll);
});

test('index returns an ok response', function (): void {
    $polls = Poll::factory()->times(3)->create([
        'expires_at' => null,
    ]);
    $pollExpired = Poll::factory()->create([
        'expires_at' => now()->subDays(1),
    ]);

    $group = Group::factory()->create([
        'is_modo' => 1,
    ]);
    $staff = User::factory()->create([
        'group_id' => $group->id,
    ]);

    $response = $this->get(route('staff.polls.index'))
        ->assertOk()
        ->assertViewIs('Staff.poll.index')
        ->assertSee('Closed')
        ->assertSee('Open');
});

test('show returns an ok response', function (): void {
    $poll = Poll::factory()->create();

    $response = $this->get(route('staff.polls.show', [$poll]))
        ->assertOk()
        ->assertViewIs('Staff.poll.show')
        ->assertViewHas('poll', $poll);
});

test('update returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $poll = Poll::factory()->hasOptions(3)->create();
    $newOption = Option::factory()->make();

    $response = $this->patch(route('staff.polls.update', [$poll]), [
        ...$poll->toArray(),
        'options' => [
            ...$poll->options->toArray(),
            [
                'id'   => 0,
                'name' => $newOption->name,
            ]
        ]
    ])
        ->assertRedirectToRoute('staff.polls.index')
        ->assertSessionHas('success', 'Your poll has been edited.');

    $this->assertDatabaseHas('options', [
        'poll_id' => $poll->id,
        'name'    => $newOption->name,
    ]);
});

// test cases...
