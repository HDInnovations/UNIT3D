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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreChatRoomRequest;
use App\Http\Requests\Staff\UpdateChatRoomRequest;
use App\Models\Chatroom;
use App\Models\User;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\ChatRoomControllerTest
 */
class ChatRoomController extends Controller
{
    /**
     * Display All Chat Rooms.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.room.index', [
            'chatrooms' => Chatroom::all(),
        ]);
    }

    /**
     * Show Form For Creating A New Chatroom.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.room.create');
    }

    /**
     * Store A New Chatroom.
     */
    public function store(StoreChatRoomRequest $request): \Illuminate\Http\RedirectResponse
    {
        Chatroom::create($request->validated());

        return to_route('staff.chatrooms.index')
            ->with('success', 'Chatroom successfully added');
    }

    /**
     * Chatroom Edit Form.
     */
    public function edit(Chatroom $chatroom): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.room.edit', [
            'chatroom' => $chatroom,
        ]);
    }

    /**
     * Update A Chatroom.
     */
    public function update(UpdateChatRoomRequest $request, Chatroom $chatroom): \Illuminate\Http\RedirectResponse
    {
        $chatroom->update($request->validated());

        return to_route('staff.chatrooms.index')
            ->with('success', 'Chatroom successfully modified');
    }

    /**
     * Delete A Chatroom.
     *
     * @throws Exception
     */
    public function destroy(Chatroom $chatroom): \Illuminate\Http\RedirectResponse
    {
        $default = Chatroom::query()
            ->when(
                \is_int(config('chat.system_chatroom')),
                fn ($query) => $query->where('id', '=', config('chat.system_chatroom')),
                fn ($query) => $query->where('name', '=', config('chat.system_chatroom')),
            )
            ->soleValue('id');

        User::whereBelongsTo($chatroom)->update(['chatroom_id' => $default]);

        $chatroom->delete();

        return to_route('staff.chatrooms.index')
            ->with('success', 'Chatroom successfully deleted');
    }
}
