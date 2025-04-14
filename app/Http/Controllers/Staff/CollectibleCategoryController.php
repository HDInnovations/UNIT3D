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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreCollectibleCategoryRequest;
use App\Http\Requests\Staff\UpdateCollectibleCategoryRequest;
use App\Models\CollectibleCategory;

class CollectibleCategoryController extends Controller
{
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.collectible-category.create');
    }

    public function store(StoreCollectibleCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        CollectibleCategory::create($request->validated('collectibleCategory'));

        return to_route('staff.collectibles.index')
            ->with('success', 'Category has been created successfully.');
    }

    public function edit(CollectibleCategory $collectibleCategory): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.collectible-category.edit', [
            'collectibleCategory' => $collectibleCategory,
        ]);
    }

    public function update(UpdateCollectibleCategoryRequest $request, CollectibleCategory $collectibleCategory): \Illuminate\Http\RedirectResponse
    {
        $collectibleCategory->update($request->validated('collectibleCategory'));

        return to_route('staff.collectibles.index')
            ->with('success', 'Category has been edited successfully.');
    }

    public function destroy(CollectibleCategory $collectibleCategory): \Illuminate\Http\RedirectResponse
    {
        $collectibleCategory->delete();

        return to_route('staff.collectibles.index')
            ->with('success', 'Category has been deleted successfully.');
    }
}
