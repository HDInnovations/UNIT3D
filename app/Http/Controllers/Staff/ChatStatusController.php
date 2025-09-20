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
use App\Http\Requests\Staff\StoreChatStatusRequest;
use App\Http\Requests\Staff\UpdateChatStatusRequest;
use App\Models\ChatStatus;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\ChatStatusControllerTest
 */
class ChatStatusController extends Controller
{
    /**
     * Chat Management.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.status.index', [
            'chatstatuses' => ChatStatus::all(),
        ]);
    }

    /**
     * Show Form For Creating A New Chat Status.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.status.create');
    }

    /**
     * Store A New Chat Status.
     */
    public function store(StoreChatStatusRequest $request): \Illuminate\Http\RedirectResponse
    {
        ChatStatus::create($request->validated());

        return to_route('staff.statuses.index')
            ->with('success', 'Chat status successfully added');
    }

    /**
     * Chat Status Edit Form.
     */
    public function edit(ChatStatus $chatStatus): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.chat.status.edit', [
            'chatstatus' => $chatStatus,
        ]);
    }

    /**
     * Update A Chat Status.
     */
    public function update(UpdateChatStatusRequest $request, ChatStatus $chatStatus): \Illuminate\Http\RedirectResponse
    {
        $chatStatus->update($request->validated());

        return to_route('staff.statuses.index')
            ->with('success', 'Chat status successfully modified');
    }

    /**
     * Delete A Chat Status.
     *
     * @throws Exception
     */
    public function destroy(ChatStatus $chatStatus): \Illuminate\Http\RedirectResponse
    {
        $chatStatus->delete();

        return to_route('staff.statuses.index')
            ->with('success', 'Chat status successfully deleted');
    }
}
