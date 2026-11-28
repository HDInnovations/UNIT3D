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

use App\Models\Chatroom;
use Database\Seeders\ChatroomSeeder;

test('create returns an ok response', function (): void {
    $this->get(route('staff.chatrooms.create'))
        ->assertOk()
        ->assertViewIs('Staff.chat.room.create');
});

test('destroy returns an ok response', function (): void {
    $this->seed(ChatroomSeeder::class);

    $chatroom = Chatroom::factory()->create();

    $this->delete(route('staff.chatrooms.destroy', [$chatroom]))
        ->assertRedirect(route('staff.chatrooms.index'))
        ->assertSessionHas('success', 'Chatroom successfully deleted');

    $this->assertModelMissing($chatroom);
});

test('edit returns an ok response', function (): void {
    $chatroom = Chatroom::factory()->create();

    $this->get(route('staff.chatrooms.edit', [$chatroom]))
        ->assertOk()
        ->assertViewIs('Staff.chat.room.edit')
        ->assertViewHas('chatroom', $chatroom);
});

test('index returns an ok response', function (): void {
    $this->get(route('staff.chatrooms.index'))
        ->assertOk()
        ->assertViewIs('Staff.chat.room.index')
        ->assertViewHas('chatrooms');
});

test('store returns an ok response', function (): void {
    $this->post(route('staff.chatrooms.store'), [
        'name' => 'Test Chatroom',
    ])
        ->assertRedirect(route('staff.chatrooms.index'))
        ->assertSessionHas('success', 'Chatroom successfully added');
});

test('update returns an ok response', function (): void {
    $chatroom = Chatroom::factory()->create();

    $this->post(route('staff.chatrooms.update', [$chatroom]), [
        'name' => 'Test Chatroom Updated',
    ])
        ->assertRedirect(route('staff.chatrooms.index'))
        ->assertSessionHas('success', 'Chatroom successfully modified');
});
