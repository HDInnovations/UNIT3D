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

use App\Models\ForumCategory;

/**
 * @see \Tests\Feature\Http\Controllers\ForumCategoryControllerTest
 */
class ForumCategoryController extends Controller
{
    /**
     * Show The Forum Category.
     */
    public function show(int $id): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        return view('forum.category-topic.index', [
            'category' => ForumCategory::query()
                ->whereHas('forums', fn ($query) => $query->authorized(canReadTopic: true))
                ->findOrFail($id),
        ]);
    }
}
