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
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\NewPost;
use App\Notifications\NewPostTag;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PostControllerTest
 */
class PostController extends Controller
{
    /**
     * PostController Constructor.
     */
    public function __construct(private readonly ChatRepository $chatRepository)
    {
    }

    /**
     * Posts Index.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('forum.post.index');
    }

    /**
     * Store A New Post To A Topic.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'content'  => 'required|min:1',
            'topic_id' => 'required|integer',
            'anon'     => 'sometimes|boolean',
        ]);

        $user = $request->user();

        $topic = Topic::authorized(canReplyTopic: true)->findOrFail($request->integer('topic_id'));

        $forum = $topic->forum;

        $post = Post::create([
            'content'  => $request->input('content'),
            'anon'     => $request->boolean('anon'),
            'user_id'  => $user->id,
            'topic_id' => $topic->id,
        ]);

        $topic->update([
            'last_post_id'         => $post->id,
            'last_post_user_id'    => $user->id,
            'num_post'             => $topic->posts()->count(),
            'last_post_created_at' => $post->created_at,
        ]);

        $forum->update([
            'num_post'             => $forum->posts()->count(),
            'num_topic'            => $forum->topics()->count(),
            'last_post_user_id'    => $user->id,
            'last_post_id'         => $post->id,
            'last_topic_id'        => $topic->id,
            'last_post_created_at' => $post->created_at,
        ]);

        // Post To Chatbox and Notify Subscribers
        $appUrl = config('app.url');
        $postUrl = \sprintf('%s/forums/topics/%s/posts/%s', $appUrl, $topic->id, $post->id);
        $realUrl = \sprintf('/forums/topics/%s/posts/%s', $topic->id, $post->id);
        $profileUrl = \sprintf('%s/users/%s', $appUrl, $user->username);

        if (config('other.staff-forum-notify') && ($forum->id == config('other.staff-forum-id') || $forum->forum_category_id == config('other.staff-forum-id'))) {
            $staffers = User::query()
                ->where('id', '!=', $user->id)
                ->whereRelation('forumPermissions', [
                    ['read_topic', '=', 1],
                    ['forum_id', '=', $forum->id],
                ])
                ->get();

            foreach ($staffers as $staffer) {
                $staffer->notify(new NewPost('staff', $user, $post));
            }
        } else {
            if ($post->anon) {
                $this->chatRepository->systemMessage(\sprintf('An anonymous user has left a reply on topic [url=%s]%s[/url]', $postUrl, $topic->name));
            } else {
                $this->chatRepository->systemMessage(\sprintf('[url=%s]%s[/url] has left a reply on topic [url=%s]%s[/url]', $profileUrl, $user->username, $postUrl, $topic->name));
            }

            $topicStarter = $topic->user;

            // Notify All Subscribers Of New Reply
            $topicStarter->notify(new NewPost('topic', $user, $post));

            $subscribers = User::query()
                ->where('id', '!=', $user->id)
                ->whereRelation('subscriptions', 'topic_id', '=', $topic->id)
                ->whereRelation('forumPermissions', [
                    ['read_topic', '=', 1],
                    ['forum_id', '=', $topic->forum_id],
                ])
                ->where(
                    fn ($query) => $query
                        ->whereRelation('notification', 'show_subscription_topic', '=', true)
                        ->orDoesntHave('notification')
                )
                ->get();

            foreach ($subscribers as $subscriber) {
                $subscriber->notify(new NewPost('subscription', $user, $post));
            }

            // Achievements
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

        // User Tagged Notification
        preg_match_all('/@([\w\-]+)/', (string) $post->content, $matches);
        $users = User::whereIn('username', $matches[1])->where('id', '!=', $user->id)->get();
        Notification::send($users, new NewPostTag($post));

        return redirect()->to($realUrl)
            ->with('success', trans('forum.reply-topic-success'));
    }

    /**
     * Edit A Post.
     */
    public function edit(Request $request, int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $user = $request->user();

        $post = Post::find($id);
        $topic = Topic::whereKey($post->topic_id)->authorized(canReadTopic: true, canReplyTopic: true)->sole();

        abort_unless($user->group->is_modo || $user->id === $post->user_id, 403);

        $forum = $topic->forum;
        $category = $forum->category;

        return view('forum.post.edit', [
            'topic'    => $topic,
            'forum'    => $forum,
            'post'     => $post,
            'category' => $category,
        ]);
    }

    /**
     * Update A Post.
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'content' => 'required|min:1',
        ]);

        $user = $request->user();

        $post = Post::findOrFail($id);
        $postUrl = \sprintf('forums/topics/%s/posts/%s', $post->topic->id, $id);

        abort_unless($user->group->is_modo || $user->id === $post->user_id, 403);

        abort_unless($post->topic()->authorized(canReplyTopic: true)->exists(), 403);

        $post->update([
            'content' => $request->input('content'),
        ]);

        return redirect()->to($postUrl)
            ->with('success', trans('forum.edit-post-success'));
    }

    /**
     * Delete A Post.
     *
     * @throws Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $post = Post::with('topic')->findOrFail($id);

        abort_unless($user->group->is_modo || $user->id === $post->user_id, 403);

        $topic = Topic::whereKey($post->topic_id)->authorized(canReplyTopic: true)->sole();

        $post->delete();

        $latestPost = $topic->latestPostSlow;
        $isTopicDeleted = false;

        if ($latestPost === null) {
            $topic->delete();
            $isTopicDeleted = true;
        } else {
            $latestPoster = $latestPost->user;
            $topic->update([
                'last_post_id'         => $latestPost->id,
                'last_post_user_id'    => $latestPoster->id,
                'num_post'             => $topic->posts()->count(),
                'last_post_created_at' => $latestPost->created_at,
            ]);
        }

        $forum = $topic->forum;
        $lastRepliedTopic = $forum->lastRepliedTopicSlow;
        $latestPost = $lastRepliedTopic->latestPostSlow;
        $latestPoster = $latestPost->user;

        $forum->update([
            'num_post'             => $forum->posts()->count(),
            'num_topic'            => $forum->topics()->count(),
            'last_post_id'         => $latestPost->id,
            'last_post_user_id'    => $latestPoster->id,
            'last_topic_id'        => $lastRepliedTopic->id,
            'last_post_created_at' => $latestPost->created_at,
        ]);

        if ($isTopicDeleted === true) {
            return to_route('forums.show', ['id' => $forum->id])
                ->with('success', trans('forum.delete-post-success'));
        }

        return to_route('topics.show', ['id' => $post->topic->id])
            ->with('success', trans('forum.delete-post-success'));
    }
}
