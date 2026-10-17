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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PrivateMessageControllerTest
 */
class ConversationController extends Controller
{
    /**
     * View/Search PM Inbox.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.conversations.index', [
            'user' => $user,
        ]);
    }

    /**
     * View A Conversation.
     */
    public function show(Request $request, User $user, Conversation $conversation): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $conversation->participants()->whereBelongsTo($user)->update([
            'read' => true,
        ]);

        return view('user.conversations.show', [
            'conversation' => $conversation->load('users.group', 'messages.sender.group'),
            'user'         => $user
        ]);
    }

    /**
     * Create Conversation Form.
     */
    public function create(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless($request->user()->is($user), 403);

        return view('user.conversations.create', [
            'user'     => $user,
            'username' => $request->query('username'),
        ]);
    }

    /**
     * Create A Conversation.
     */
    public function store(StoreConversationRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $recipient = User::query()->where('username', '=', $request->validated('receiver_username'))->sole();

        abort_if($recipient->id === User::SYSTEM_USER_ID, 403);

        $conversation = Conversation::query()->create($request->validated('conversation'));

        PrivateMessage::query()->create([
            'conversation_id' => $conversation->id,
            'message'         => $request->validated('message'),
            'sender_id'       => $user->id,
        ]);

        $conversation->users()->sync([$user->id => ['read' => true], $recipient->id]);

        return to_route('users.conversations.show', ['user' => $user, 'conversation' => $conversation])
            ->with('success', trans('pm.sent-success'));
    }

    /**
     * Reply To A Message.
     */
    public function update(UpdateConversationRequest $request, User $user, Conversation $conversation): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        abort_if($conversation->participants()->withTrashed()->where('user_id', '=', User::SYSTEM_USER_ID)->exists(), 403, 'You cannot reply to the system');

        PrivateMessage::query()->create([
            'sender_id'       => $user->id,
            'conversation_id' => $conversation->id,
            'message'         => $request->validated('message'),
        ]);

        $conversation->participants()->update([
            'read'       => false,
            'deleted_at' => null,
        ]);

        $conversation->touch();

        return to_route('users.conversations.show', [
            'user'         => $user,
            'conversation' => $conversation
        ])
            ->with('success', trans('pm.sent-success'));
    }

    /**
     * Delete A Conversation.
     *
     * @throws Exception
     */
    public function destroy(Request $request, User $user, Conversation $conversation): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $conversation->participants()->whereBelongsTo($user)->delete();

        return to_route('users.conversations.index', ['user' => $user])
            ->with('success', trans('pm.delete-success'));
    }

    /**
     * Empty Private Message Inbox.
     */
    public function massDestroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $user->participations()->delete();

        return to_route('users.conversations.index', ['user' => $user])
            ->with('success', trans('pm.delete-success'));
    }

    /**
     * Mark All Messages As Read.
     */
    public function massUpdate(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        $user->participations()->update([
            'read' => true,
        ]);

        return to_route('users.conversations.index', ['user' => $user])
            ->with('success', trans('pm.all-marked-read'));
    }
}
