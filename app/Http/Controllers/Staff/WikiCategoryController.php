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

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreWikiCategoryRequest;
use App\Http\Requests\Staff\UpdateWikiCategoryRequest;
use App\Models\WikiCategory;

class WikiCategoryController extends Controller
{
    /**
     * Display All Categories.
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.wiki-category.index', [
            'wikiCategories' => WikiCategory::query()
                ->with(['wikis' => fn ($query) => $query->orderBy('name')])
                ->orderBy('position')
                ->get(),
        ]);
    }

    /**
     * Show Form For Creating A New Category.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.wiki-category.create');
    }

    /**
     * Store A Category.
     */
    public function store(StoreWikiCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        WikiCategory::create($request->validated());

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki category successfully added');
    }

    /**
     * Category Edit Form.
     */
    public function edit(WikiCategory $wikiCategory): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.wiki-category.edit', [
            'wikiCategory' => $wikiCategory,
        ]);
    }

    /**
     * Update A Category.
     */
    public function update(UpdateWikiCategoryRequest $request, WikiCategory $wikiCategory): \Illuminate\Http\RedirectResponse
    {
        $wikiCategory->update($request->validated());

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki category successfully modified');
    }

    /**
     * Destroy A Category.
     */
    public function destroy(WikiCategory $wikiCategory): \Illuminate\Http\RedirectResponse
    {
        $wikiCategory->delete();

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki category successfully deleted');
    }
}
