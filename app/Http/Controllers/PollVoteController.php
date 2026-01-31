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

namespace App\Http\Controllers;

use App\Http\Requests\StorePollVoteRequest;
use App\Models\Option;
use App\Models\Poll;
use App\Models\Voter;
use App\Repositories\ChatRepository;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\PollControllerTest
 */
class PollVoteController extends Controller
{
    /**
     * PollController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Vote On A Poll.
     */
    public function store(StorePollVoteRequest $request, Poll $poll): \Illuminate\Http\RedirectResponse
    {
        if (Voter::query()->whereBelongsTo($poll)->whereBelongsTo($request->user())->exists()) {
            return to_route('polls.votes.index', ['poll' => $poll])
                ->withErrors(trans('poll.already-voted-error'));
        }

        Option::query()->whereIn('id', $request->validated('options'))->increment('votes');

        $poll->users()->attach($request->user());

        $this->chatRepository->systemMessage(
            \sprintf('[url=%s]%s[/url] has voted on poll [url=%s]%s[/url]', href_profile($request->user()), $request->user()->username, href_poll($poll), $poll->title)
        );

        return to_route('polls.votes.index', ['poll' => $poll])
            ->with('success', trans('poll.vote-counted'));
    }

    /**
     * Show A Polls Results.
     */
    public function index(Poll $poll): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('poll.result', [
            'poll' => $poll,
        ]);
    }
}
