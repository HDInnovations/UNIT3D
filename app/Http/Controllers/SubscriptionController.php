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
use Illuminate\Http\Request;

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
    public function store(StoreSubscriptionRequest $request): \Illuminate\Http\RedirectResponse
    {
        switch (true) {
            case $request->has('forum_id') === true:
                abort_unless(
                    Forum::query()
                        ->where('id', '=', $request->forum_id)
                        ->authorized(canReadTopic: true)
                        ->exists(),
                    403
                );
                $request->user()->subscribedForums()->attach($request->forum_id);

                return back()
                    ->with('success', __('application-messages.flash.forum-subscribed'));

            case $request->has('topic_id') === true:
                abort_unless(
                    Topic::query()
                        ->where('id', '=', $request->topic_id)
                        ->authorized(canReadTopic: true)
                        ->exists(),
                    403
                );

                $request->user()->subscribedTopics()->attach($request->topic_id);

                return back()
                    ->with('success', __('application-messages.flash.topic-subscribed'));

            default:
                return back()->withErrors([__('application-messages.validation.subscribe-failed')]);
        }
    }

    /**
     * Destroy a subscription.
     */
    public function destroy(Request $request, Subscription $subscription): \Illuminate\Http\RedirectResponse
    {
        abort_unless($subscription->user_id === $request->user()->id, 403);

        $subscription->delete();

        return back()->with('success', __('application-messages.flash.unsubscribed'));
    }
}
