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

use App\Achievements\UserMade100Posts;
use App\Achievements\UserMade200Posts;
use App\Achievements\UserMade25Posts;
use App\Achievements\UserMade300Posts;
use App\Achievements\UserMade400Posts;
use App\Achievements\UserMade500Posts;
use App\Achievements\UserMade50Posts;
use App\Achievements\UserMade600Posts;
use App\Achievements\UserMade700Posts;
use App\Achievements\UserMade800Posts;
use App\Achievements\UserMade900Posts;
use App\Achievements\UserMadeFirstPost;
use App\Models\Forum;
use App\Models\ForumPermission;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\NewTopic;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\TopicControllerTest
 */
class TopicController extends Controller
{
    /**
     * TopicController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Topics index.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('forum.topic.index');
    }

    /**
     * Show Topic.
     */
    public function show(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $topic = Topic::with('user', 'forum.category')->authorized(canReadTopic: true)->findOrFail($id);

        $subscription = Subscription::where('user_id', '=', $user->id)->where('topic_id', '=', $id)->first();

        $topic->views++;
        $topic->save();

        return view('forum.topic.show', [
            'topic'        => $topic,
            'subscription' => $subscription,
            'permission'   => ForumPermission::query()
                ->where('group_id', '=', $user->group_id)
                ->where('forum_id', '=', $topic->forum_id)
                ->first(),
        ]);
    }

    /**
     * Create Topic.
     */
    public function create(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $forum = Forum::with('category')->authorized(canStartTopic: true)->findOrFail($id);

        return view('forum.forum-topic.create', [
            'forum' => $forum,
        ]);
    }

    /**
     * Store Topic.
     */
    public function store(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
            'anon'    => 'sometimes|boolean',
        ]);

        $user = $request->user();
        $forum = Forum::authorized(canStartTopic: true)->findOrFail($id);

        $topic = Topic::create([
            'name'               => $request->title,
            'state'              => 'open',
            'first_post_user_id' => $user->id,
            'last_post_user_id'  => $user->id,
            'views'              => 0,
            'priority'           => 0,
            'forum_id'           => $forum->id,
            'num_post'           => 1,
        ]);

        $post = Post::create([
            'content'  => $request->input('content'),
            'anon'     => $request->boolean('anon'),
            'user_id'  => $user->id,
            'topic_id' => $topic->id,
        ]);

        $forum->update([
            'num_topic'            => $forum->topics()->count(),
            'num_post'             => $forum->posts()->count(),
            'last_topic_id'        => $topic->id,
            'last_post_id'         => $post->id,
            'last_post_user_id'    => $user->id,
            'last_post_created_at' => $post->created_at,
        ]);

        $topic->update([
            'last_post_id'         => $post->id,
            'last_post_created_at' => $post->created_at,
        ]);

        // Post To ShoutBox
        $appUrl = config('app.url');
        $topicUrl = \sprintf('%s/forums/topics/%s', $appUrl, $topic->id);
        $profileUrl = \sprintf('%s/users/%s', $appUrl, $user->username);

        if (config('other.staff-forum-notify') && ($forum->id == config('other.staff-forum-id') || $forum->forum_category_id == config('other.staff-forum-id'))) {
            $staffers = User::query()
                ->where('id', '!=', $user->id)
                ->whereRelation('group', 'is_modo', '=', true)
                ->get();

            foreach ($staffers as $staffer) {
                $staffer->notify(new NewTopic('staff', $user, $topic, $post));
            }
        } else {
            if ($post->anon) {
                $this->chatRepository->systemMessage(\sprintf('An anonymous user has created a new topic [url=%s]%s[/url]', $topicUrl, $topic->name));
            } else {
                $this->chatRepository->systemMessage(\sprintf('[url=%s]%s[/url] has created a new topic [url=%s]%s[/url]', $profileUrl, $user->username, $topicUrl, $topic->name));
            }

            $subscribers = User::query()
                ->where('id', '!=', $topic->first_post_user_id)
                ->whereRelation('subscriptions', 'forum_id', '=', $topic->forum_id)
                ->whereRelation('forumPermissions', [
                    ['read_topic', '=', 1],
                    ['forum_id', '=', $topic->forum_id],
                ])
                ->where(
                    fn ($query) => $query
                        ->whereRelation('notification', 'show_subscription_forum', '=', true)
                        ->orDoesntHave('notification')
                )
                ->get();

            foreach ($subscribers as $subscriber) {
                $subscriber->notify(new NewTopic('forum', $user, $topic, $post));
            }

            //Achievements
            $user->unlock(new UserMadeFirstPost());
            $user->addProgress(new UserMade25Posts(), 1);
            $user->addProgress(new UserMade50Posts(), 1);
            $user->addProgress(new UserMade100Posts(), 1);
            $user->addProgress(new UserMade200Posts(), 1);
            $user->addProgress(new UserMade300Posts(), 1);
            $user->addProgress(new UserMade400Posts(), 1);
            $user->addProgress(new UserMade500Posts(), 1);
            $user->addProgress(new UserMade600Posts(), 1);
            $user->addProgress(new UserMade700Posts(), 1);
            $user->addProgress(new UserMade800Posts(), 1);
            $user->addProgress(new UserMade900Posts(), 1);
        }

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'Topic created successfully!');
    }

    /**
     * Edit Topic.
     */
    public function edit(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();

        $topic = Topic::with('forum.category')->authorized(canReadTopic: true, canReplyTopic: true)->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $topic->first_post_user_id, 403);

        $categories = Forum::with('category:id,name')
            ->authorized(canReadTopic: true, canStartTopic: true)
            ->get()
            ->groupBy('category.name');

        return view('forum.topic.edit', [
            'topic'      => $topic,
            'categories' => $categories
        ]);
    }

    /**
     * Update Topic.
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'required|min:1',
            'forum_id' => 'required|integer|exists:forums,id',
        ]);

        $topic = Topic::query()->authorized(canReadTopic: true, canReplyTopic: true)->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $topic->first_post_user_id, 403);

        $newForum = Forum::authorized(canStartTopic: true)->whereKey($request->forum_id)->sole();

        $oldForum = $topic->forum;

        $topic->update([
            'name'     => $request->name,
            'forum_id' => $newForum->id,
        ]);

        if ($oldForum->id === $newForum->id) {
            $lastRepliedTopic = $newForum->lastRepliedTopicSlow;

            if ($lastRepliedTopic->id === $newForum->last_topic_id) {
                $latestPost = $lastRepliedTopic->latestPostSlow;

                $newForum->updated_at = $latestPost->created_at;
                $newForum->save();
            }
        } else {
            $lastRepliedTopic = $oldForum->lastRepliedTopicSlow;
            $latestPost = $lastRepliedTopic->latestPostSlow;
            $latestPoster = $latestPost->user;

            $oldForum->update([
                'num_topic'            => $oldForum->topics()->count(),
                'num_post'             => $oldForum->posts()->count(),
                'last_topic_id'        => $lastRepliedTopic->id,
                'last_post_id'         => $latestPost->id,
                'last_post_user_id'    => $latestPoster->id,
                'last_post_created_at' => $latestPost->created_at,
            ]);

            $lastRepliedTopic = $newForum->lastRepliedTopicSlow;
            $latestPost = $lastRepliedTopic->latestPostSlow;
            $latestPoster = $latestPost->user;

            $newForum->update([
                'num_topic'            => $newForum->topics()->count(),
                'num_post'             => $newForum->posts()->count(),
                'last_topic_id'        => $lastRepliedTopic->id,
                'last_post_id'         => $latestPost->id,
                'last_post_user_id'    => $latestPoster->id,
                'last_post_created_at' => $latestPost->created_at,
            ]);
        }

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'Topic successfully edited');
    }

    /**
     * Delete The Topic and The Posts.
     *
     * @throws Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);

        $topic->posts()->delete();
        $topic->delete();

        $forum = $topic->forum;
        $lastRepliedTopic = $forum->lastRepliedTopicSlow;
        $latestPost = $lastRepliedTopic->latestPostSlow;
        $latestPoster = $latestPost->user;

        $topic->forum()->update([
            'num_topic'            => $forum->topics()->count(),
            'num_post'             => $forum->posts()->count(),
            'last_topic_id'        => $lastRepliedTopic->id,
            'last_post_id'         => $latestPost->id,
            'last_post_user_id'    => $latestPoster->id,
            'last_post_created_at' => $latestPost->created_at,
        ]);

        return to_route('forums.show', ['id' => $forum->id])
            ->with('success', 'This topic is now deleted!');
    }

    /**
     * Close The Topic.
     */
    public function close(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->state = 'close';
        $topic->save();

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'This topic is now closed!');
    }

    /**
     * Open The Topic.
     */
    public function open(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->state = 'open';
        $topic->save();

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'This topic is now open!');
    }

    /**
     * Pin The Topic.
     */
    public function pin(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->priority = $request->integer('priority');
        $topic->save();

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'This topic is now pinned!');
    }

    /**
     * Unpin The Topic.
     */
    public function unpin(int $id): \Illuminate\Http\RedirectResponse
    {
        $topic = Topic::findOrFail($id);
        $topic->priority = 0;
        $topic->save();

        return to_route('topics.show', ['id' => $topic->id])
            ->with('success', 'This topic is now unpinned!');
    }

    /**
     * Redirect to the appropriate topic page.
     */
    public function permalink(int $topicId, int $postId): \Illuminate\Http\RedirectResponse
    {
        $index = Post::where('topic_id', '=', $topicId)->where('id', '<', $postId)->count();

        return to_route('topics.show', [
            'id'   => $topicId,
            'page' => intdiv($index, 25) + 1
        ])
            ->withFragment('post-'.$postId);
    }

    /**
     * Redirect to the appropriate topic page for the latest post.
     */
    public function latestPermalink(int $id): \Illuminate\Http\RedirectResponse
    {
        $post = Post::query()
            ->selectRaw('MAX(id) as id')
            ->selectRaw('count(*) as post_count')
            ->where('topic_id', '=', $id)
            ->first();

        return to_route('topics.show', [
            'id'   => $id,
            'page' => intdiv($post?->post_count === null ? 0 : $post->post_count - 1, 25) + 1
        ])
            ->withFragment('post-'.($post->id ?? 0));
    }
}
