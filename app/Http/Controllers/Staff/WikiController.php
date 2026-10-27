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
use App\Http\Requests\Staff\StoreWikiRequest;
use App\Http\Requests\Staff\UpdateWikiRequest;
use App\Models\Wiki;
use App\Models\WikiCategory;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    /**
     * Page Add Form.
     */
    public function create(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.wiki.create', [
            'wikiCategoryId' => $request->integer('wikiCategoryId'),
            'wikiCategories' => WikiCategory::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Store A New Page.
     */
    public function store(StoreWikiRequest $request): \Illuminate\Http\RedirectResponse
    {
        Wiki::query()->create($request->validated());

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki has been created successfully');
    }

    /**
     * Page Edit Form.
     */
    public function edit(Wiki $wiki): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('Staff.wiki.edit', [
            'wiki'           => $wiki,
            'wikiCategories' => WikiCategory::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Edit A Page.
     */
    public function update(UpdateWikiRequest $request, Wiki $wiki): \Illuminate\Http\RedirectResponse
    {
        $wiki->update($request->validated());

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki has been edited successfully');
    }

    /**
     * Delete A Page.
     */
    public function destroy(Wiki $wiki): \Illuminate\Http\RedirectResponse
    {
        $wiki->delete();

        return to_route('staff.wiki_categories.index')
            ->with('success', 'Wiki has been deleted successfully');
    }
}
