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

use App\Models\Conversation;
use App\Models\User;

test('store returns an ok response', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1)->post(route('users.conversations.store', [$user1]), [
        'conversation' => [
            'subject' => 'test',
        ],
        'message'           => 'test message',
        'receiver_username' => $user2->username,
    ])
        ->assertRedirect()
        ->assertSessionHas('success', __('pm.sent-success'));

    $this->assertDatabaseHas('conversations', [
        'subject' => 'test',
    ]);

    $this->assertDatabaseHas('participants', [
        'user_id' => $user1->id,
    ]);

    $this->assertDatabaseHas('participants', [
        'user_id' => $user2->id,
    ]);

    $this->assertDatabaseHas('private_messages', [
        'message'   => 'test message',
        'sender_id' => $user1->id,
    ]);
});

test('update returns an ok response', function (): void {
    $conversation = Conversation::factory()
        ->has(User::factory())
        ->has(User::factory())
        ->create();

    $this->actingAs($conversation->users()->first())
        ->patch(route('users.conversations.update', [$conversation->users()->first(), $conversation]), [
            'message' => 'test message 2',
        ])
        ->assertRedirect()
        ->assertSessionHas('success', __('pm.sent-success'));

    $this->assertDatabaseHas('private_messages', [
        'message'         => 'test message 2',
        'sender_id'       => $conversation->users()->value('users.id'),
        'conversation_id' => $conversation->id,
    ]);
});
