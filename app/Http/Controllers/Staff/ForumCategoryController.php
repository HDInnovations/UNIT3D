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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreForumCategoryRequest;
use App\Http\Requests\Staff\UpdateForumCategoryRequest;
use App\Models\ForumCategory;

class ForumCategoryController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.forum-category.index', [
            'categories' => ForumCategory::query()
                ->with(['forums' => fn ($query) => $query->orderBy('position')])
                ->orderBy('position')
                ->get(),
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.forum-category.create');
    }

    public function store(StoreForumCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        ForumCategory::query()->create($request->validated());

        return to_route('staff.forum_categories.index')
            ->with('success', 'Forum has been created successfully');
    }

    public function edit(ForumCategory $forumCategory): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.forum-category.edit', [
            'forumCategory' => $forumCategory,
        ]);
    }

    public function update(UpdateForumCategoryRequest $request, ForumCategory $forumCategory): \Illuminate\Http\RedirectResponse
    {
        $forumCategory->update($request->validated());

        return to_route('staff.forum_categories.index')
            ->with('success', 'Forum has been edited successfully');
    }

    public function destroy(ForumCategory $forumCategory): \Illuminate\Http\RedirectResponse
    {
        $forumCategory->delete();

        return to_route('staff.forum_categories.index')
            ->with('success', 'Forum has been deleted successfully');
    }
}
