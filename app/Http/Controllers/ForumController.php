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

use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\Staff\ForumControllerTest
 */
class ForumController extends Controller
{
    /**
     * Show All Forums.
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('forum.index', [
            'categories' => ForumCategory::query()
                ->with([
                    'forums'              => fn ($query) => $query->authorized(canReadTopic: true)->orderBy('position'),
                    'forums.latestPoster' => fn ($query) => $query->withTrashed(),
                    'forums.lastRepliedTopic',
                ])
                ->orderBy('position')
                ->get()
                ->filter(fn ($category) => $category->forums->isNotEmpty()),
            'num_posts' => cache()->remember(
                'post-count:by-group-id:'.$request->user()->group_id,
                3600,
                fn () => Post::query()->authorized(canReadTopic: true)->count()
            ),
            'num_forums' => cache()->remember(
                'forum-count:by-group-id:'.$request->user()->group_id,
                3600,
                fn () => Forum::query()->authorized(canReadTopic: true)->count()
            ),
            'num_topics' => cache()->remember(
                'topic-count:by-group-id:'.$request->user()->group_id,
                3600,
                fn () => Topic::query()->authorized(canReadTopic: true)->count()
            ),
        ]);
    }

    /**
     * Show Forums And Topics Inside.
     */
    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        return view('forum.forum-topic.index', [
            'forum' => Forum::query()
                ->with('category')
                ->authorized(canReadTopic: true)
                ->findOrFail($id),
        ]);
    }
}
