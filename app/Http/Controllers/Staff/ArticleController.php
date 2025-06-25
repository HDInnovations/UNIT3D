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

use App\Data\Staff\ArticleData;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\UnreadArticle;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * @see \Tests\Feature\Http\Controllers\ArticleControllerTest
 */
class ArticleController extends Controller
{
    /**
     * Display All Articles.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.article.index', [
            'articles' => Article::latest()
                ->with('user:id,username')
                ->withCount('comments')
                ->paginate(25),
        ]);
    }

    /**
     * Article Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.article.create');
    }

    /**
     * Store A New Article.
     */
    public function store(ArticleData $data): \Illuminate\Http\RedirectResponse
    {
        if ($data->image !== null) {
            $filename = 'article-'.uniqid('', true).'.'.$data->image->getClientOriginalExtension();
            $path = Storage::disk('article-images')->path($filename);
            Image::make($data->image->getRealPath())->fit(75, 75)->encode('png', 100)->save($path);
        }

        $article = Article::create(['image' => $filename ?? null] + $data->toArray());

        UnreadArticle::query()->insertUsing(
            ['article_id', 'user_id'],
            User::query()
                ->selectRaw('?', [$article->id])
                ->addSelect('id')
                ->whereHas('group', fn ($query) => $query->whereNotIn('slug', ['validating', 'pruned', 'banned', 'disabled']))
        );

        return to_route('staff.articles.index')
            ->with('success', 'Your article has successfully published!');
    }

    /**
     * Article Edit Form.
     */
    public function edit(Article $article): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.article.edit', [
            'article' => $article,
        ]);
    }

    /**
     * Edit A Article.
     */
    public function update(ArticleData $data, Article $article): \Illuminate\Http\RedirectResponse
    {
        if ($data->image !== null) {
            $filename = 'article-'.uniqid('', true).'.'.$data->image->getClientOriginalExtension();
            $path = Storage::disk('article-images')->path($filename);
            Image::make($data->image->getRealPath())->fit(75, 75)->encode('png', 100)->save($path);

            if ($article->image !== null) {
                Storage::disk('article-images')->delete($article->image);
            }
        }

        $article->update(['image' => $filename ?? null,] + $data->toArray());

        return to_route('staff.articles.index')
            ->with('success', 'Your article changes have successfully published!');
    }

    /**
     * Delete A Article.
     *
     * @throws Exception
     */
    public function destroy(Article $article): \Illuminate\Http\RedirectResponse
    {
        $article->comments()->delete();
        $article->delete();

        return to_route('staff.articles.index')
            ->with('success', 'Article has successfully been deleted');
    }
}
