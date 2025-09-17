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

use App\Models\Article;
use App\Models\Comment;
use App\Models\FeaturedTorrent;
use App\Models\Group;
use App\Models\Poll;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\HomeControllerTest
 */
class HomeController extends Controller
{
    /**
     * Display Home Page.
     *
     * @throws Exception
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        // For Cache
        $expiresAt = [now()->addMinutes(5), now()->addMinutes(10)];

        // Authorized User
        $user = $request->user()->load('settings');

        return view('home.index', [
            'user'   => $user,
            'blocks' => collect(
                [
                    'news', 'chat', 'featured', 'random_media', 'poll',
                    'top_torrents', 'top_users', 'latest_topics', 'latest_posts',
                    'latest_comments', 'online'
                ]
            )
                ->map(fn ($block) => [
                    'key'      => $block,
                    'visible'  => $user->settings->{"{$block}_block_visible"},
                    'position' => $user->settings->{"{$block}_block_position"},
                ])
                ->sortBy('position')
                ->filter(fn ($block) => $block['visible'])
                ->pluck('key')
                ->toArray(),
            'users' => cache()->flexible(
                'online_users:by-group:'.auth()->user()->group_id,
                $expiresAt,
                fn () => User::with('group', 'privacy')
                    ->withCount([
                        'warnings' => function (Builder $query): void {
                            $query->whereNotNull('torrent')->where('active', true);
                        },
                    ])
                    ->where('last_action', '>', now()->subMinutes(60))
                    ->orderByRaw('(select position from "groups" where "groups".id = users.group_id), group_id, username')
                    ->get()
                    ->sortBy(fn ($user) => $user->privacy?->hidden || ! $user->isVisible($user, 'other', 'show_online')),
            ),
            'groups' => cache()->flexible(
                'user-groups',
                $expiresAt,
                fn () => Group::select([
                    'id',
                    'name',
                    'color',
                    'effect',
                    'icon',
                ])
                    ->oldest('position')
                    ->get()
            ),
            'articles' => Article::query()
                ->latest()
                ->limit(3)
                ->withExists(['unreads' => fn ($query) => $query->whereBelongsTo($user)])
                ->get(),
            'topics' => Topic::query()
                ->with(['user', 'user.group', 'latestPoster', 'reads' => fn ($query) => $query->whereBelongsTo($user)])
                ->authorized(canReadTopic: true)
                ->latest()
                ->take(5)
                ->get(),
            'posts' => cache()->flexible(
                'latest_posts:by-group:'.auth()->user()->group_id,
                $expiresAt,
                fn () => Post::query()
                    ->with('user', 'user.group', 'topic:id,name')
                    ->withCount('likes', 'dislikes', 'authorPosts', 'authorTopics')
                    ->withSum('tips', 'bon')
                    ->withExists([
                        'likes'    => fn ($query) => $query->where('user_id', '=', auth()->id()),
                        'dislikes' => fn ($query) => $query->where('user_id', '=', auth()->id()),
                    ])
                    ->authorized(canReadTopic: true)
                    ->latest()
                    ->take(5)
                    ->get(),
            ),
            'comments' => cache()->flexible(
                'latest_comments',
                $expiresAt,
                fn () => Comment::query()
                    ->with('user', 'user.group', 'commentable')
                    ->whereHasMorph('commentable', [\App\Models\Torrent::class])
                    ->orWhereHasMorph('commentable', [\App\Models\TorrentRequest::class])
                    ->latest()
                    ->take(5)
                    ->get(),
            ),
            'featured' => cache()->flexible(
                'latest_featured',
                $expiresAt,
                fn () => FeaturedTorrent::with([
                    'torrent' => ['resolution', 'type', 'category'],
                    'user.group',
                ])->get(),
            ),
            'poll' => cache()->flexible('latest_poll', $expiresAt, function () {
                return Poll::where(function ($query): void {
                    $query->where('expires_at', '>', now())
                        ->orWhereNull('expires_at');
                })->latest()->first();
            }),
        ]);
    }
}
