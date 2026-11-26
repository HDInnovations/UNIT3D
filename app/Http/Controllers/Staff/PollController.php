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
use App\Http\Requests\Staff\StorePollRequest;
use App\Http\Requests\UpdatePollRequest;
use App\Models\Chatroom;
use App\Models\Poll;
use App\Notifications\Chatbox\PollCreate;
use App\Notifications\Chatbox\PollUpdate;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\PollControllerTest
 */
class PollController extends Controller
{
    /**
     * Display All Polls.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.poll.index', [
            'polls' => Poll::query()->latest()->paginate(25),
        ]);
    }

    /**
     * Show A Poll.
     */
    public function show(Poll $poll): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.poll.show', [
            'poll' => $poll,
        ]);
    }

    /**
     * Poll Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.poll.create');
    }

    /**
     * Store A New Poll.
     */
    public function store(StorePollRequest $request): \Illuminate\Http\RedirectResponse
    {
        $poll = Poll::query()->create($request->only('title', 'expiresAt', 'multipleChoice', 'userId')->toArray());

        $poll->options()->upsert($request->options->toArray(), ['id'], []);

        Chatroom::systemChatroom()->notify(new PollCreate($poll));

        return to_route('staff.polls.index')
            ->with('success', 'Your poll has been created.');
    }

    /**
     * Poll Edit Form.
     */
    public function edit(Poll $poll): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.poll.edit', [
            'poll' => $poll,
        ]);
    }

    /**
     * Update A New Poll.
     *
     * @throws Exception
     */
    public function update(UpdatePollRequest $request, Poll $poll): \Illuminate\Http\RedirectResponse
    {
        $poll->update($request->only('title', 'expiresAt', 'multipleChoice')->toArray());

        $poll->options()
            ->whereNotIn('id', array_column($request->options->toArray(), 'id'))
            ->delete();

        $poll->options()->upsert($request->options->toArray(), ['id'], ['name']);

        Chatroom::systemChatroom()->notify(new PollUpdate($poll));

        return to_route('staff.polls.index')
            ->with('success', 'Your poll has been edited.');
    }

    /**
     * Delete A Poll.
     *
     * @throws Exception
     */
    public function destroy(Poll $poll): \Illuminate\Http\RedirectResponse
    {
        $poll->options()->delete();
        $poll->delete();

        return to_route('staff.polls.index')
            ->with('success', 'Poll has successfully been deleted');
    }
}
