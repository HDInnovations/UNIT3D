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

use App\Http\Requests\StoreSubscriptionRequest;
use App\Models\Forum;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\SubscriptionControllerTest
 */
class SubscriptionController extends Controller
{
    /**
     * View Subscribed Forums & Topics.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('forum.subscriptions');
    }

    /**
     * Store a subscription.
     */
    public function store(StoreSubscriptionRequest $request, #[CurrentUser] User $currentUser): \Illuminate\Http\RedirectResponse
    {
        switch (true) {
            case $request->forumId !== null:
                abort_unless(
                    Forum::query()
                        ->where('id', '=', $request->forumId)
                        ->authorized(canReadTopic: true)
                        ->exists(),
                    403
                );
                $currentUser->subscribedForums()->attach($request->forumId);

                return back()
                    ->with('success', 'You are now subscribed to this forum. You will now receive site notifications when a topic is started.');

            case $request->topicId !== null:
                abort_unless(
                    Topic::query()
                        ->where('id', '=', $request->topicId)
                        ->authorized(canReadTopic: true)
                        ->exists(),
                    403
                );

                $currentUser->subscribedTopics()->attach($request->topicId);

                return back()
                    ->with('success', 'You are now subscribed to this topic. You will now receive site notifications when a reply is left.');

            default:
                return back()->withErrors(['Failed to subscribe.']);
        }
    }

    /**
     * Destroy a subscription.
     */
    public function destroy(Request $request, Subscription $subscription): \Illuminate\Http\RedirectResponse
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);

        $subscription->delete();

        return back()->with('success', 'You are now unsubscribed.');
    }
}
