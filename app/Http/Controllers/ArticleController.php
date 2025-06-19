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
use Illuminate\Http\Request;

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
        return view('article.index', [
            'articles' => Article::latest()->paginate(6),
        ]);
    }

    /**
     * Show A Article.
     */
    public function show(Request $request, Article $article): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $article->unreads()->whereBelongsTo($request->user())->delete();

        return view('article.show', [
            'article' => $article->load(['user', 'comments']),
        ]);
    }
}
