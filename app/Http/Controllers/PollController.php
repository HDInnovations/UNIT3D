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

use App\Models\Poll;
use App\Models\Voter;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\PollControllerTest
 */
class PollController extends Controller
{
    /**
     * Show All Polls.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('poll.latest', [
            'polls' => Poll::query()->latest()->paginate(15),
        ]);
    }

    /**
     * Show A Poll.
     */
    public function show(Request $request, Poll $poll): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        if (Voter::query()->whereBelongsTo($poll)->whereBelongsTo($request->user())->exists()) {
            return to_route('polls.votes.index', ['poll' => $poll])
                ->withInfo(trans('poll.already-voted-result'));
        }

        if ($poll->expires_at?->isPast()) {
            return to_route('polls.votes.index', ['poll' => $poll])
                ->withInfo(trans('poll.expired-voted-result'));
        }

        return view('poll.show', ['poll' => $poll]);
    }
}
