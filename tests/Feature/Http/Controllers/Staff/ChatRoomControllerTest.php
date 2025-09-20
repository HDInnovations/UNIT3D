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

use App\Http\Controllers\Staff\ChatRoomController;
use App\Http\Requests\Staff\StoreChatRoomRequest;
use App\Http\Requests\Staff\UpdateChatRoomRequest;
use App\Models\Chatroom;
use App\Models\Group;
use App\Models\User;
use Database\Seeders\ChatroomSeeder;

beforeEach(function (): void {
    $this->staffUser = User::factory()->create([
        'group_id' => fn () => Group::factory()->create([
            'is_owner' => true,
            'is_admin' => true,
            'is_modo'  => true,
        ])->id,
    ]);
});

test('create returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.chatrooms.create'));
    $response->assertOk();
    $response->assertViewIs('Staff.chat.room.create');
});

test('destroy returns an ok response', function (): void {
    $this->seed(ChatroomSeeder::class);

    $chatroom = Chatroom::factory()->create();

    $response = $this->actingAs($this->staffUser)->delete(route('staff.chatrooms.destroy', [$chatroom]));
    $response->assertRedirect(route('staff.chatrooms.index'));
    $response->assertSessionHas('success', 'Chatroom successfully deleted');

    $this->assertModelMissing($chatroom);
});

test('edit returns an ok response', function (): void {
    $chatroom = Chatroom::factory()->create();

    $response = $this->actingAs($this->staffUser)->get(route('staff.chatrooms.edit', [$chatroom]));
    $response->assertOk();
    $response->assertViewIs('Staff.chat.room.edit');
    $response->assertViewHas('chatroom', $chatroom);
});

test('index returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->get(route('staff.chatrooms.index'));
    $response->assertOk();
    $response->assertViewIs('Staff.chat.room.index');
    $response->assertViewHas('chatrooms');
});

test('store validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ChatRoomController::class,
        'store',
        StoreChatRoomRequest::class
    );
});

test('store returns an ok response', function (): void {
    $response = $this->actingAs($this->staffUser)->post(route('staff.chatrooms.store'), [
        'name' => 'Test Chatroom',
    ]);
    $response->assertRedirect(route('staff.chatrooms.index'));
    $response->assertSessionHas('success', 'Chatroom successfully added');
});

test('update validates with a form request', function (): void {
    $this->assertActionUsesFormRequest(
        ChatRoomController::class,
        'update',
        UpdateChatRoomRequest::class
    );
});

test('update returns an ok response', function (): void {
    $chatroom = Chatroom::factory()->create();

    $response = $this->actingAs($this->staffUser)->post(route('staff.chatrooms.update', [$chatroom]), [
        'name' => 'Test Chatroom Updated',
    ]);
    $response->assertRedirect(route('staff.chatrooms.index'));
    $response->assertSessionHas('success', 'Chatroom successfully modified');
});
