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

use App\Models\Poll;
use App\Models\User;
use Database\Seeders\UserSeeder;

test('index returns an ok response', function (): void {
    $poll = Poll::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('polls.votes.index', [$poll]))
        ->assertOk()
        ->assertViewIs('poll.result')
        ->assertViewHas('poll', $poll);
});

test('store returns an ok response', function (): void {
    $this->seed(UserSeeder::class);
    $poll = Poll::factory()->hasOptions(3)->create();
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('polls.votes.store', [$poll]), [
        'options' => [
            $poll->options->value('id'),
        ],
    ])
        ->assertRedirectToRoute('polls.votes.index', [$poll])
        ->assertSessionHas('success', __('poll.vote-counted'));

    $this->assertDatabaseHas('voters', [
        'poll_id' => $poll->id,
        'user_id' => $user->id,
    ]);
});

// test cases...
