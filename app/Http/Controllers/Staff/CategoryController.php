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
use App\Http\Requests\Staff\StoreCategoryRequest;
use App\Http\Requests\Staff\UpdateCategoryRequest;
use App\Models\Category;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * @see \Tests\Feature\Http\Controllers\CategoryControllerTest
 */
class CategoryController extends Controller
{
    /**
     * Display All Categories.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.category.index', [
            'categories' => Category::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Show Form For Creating A New Category.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.category.create');
    }

    /**
     * Store A Category.
     */
    public function store(StoreCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        if ($request->image !== null) {
            $filename = 'category-'.uniqid('', true).'.'.$request->image->getClientOriginalExtension();
            $path = Storage::disk('category-images')->path($filename);
            Image::make($request->image->getRealPath())->fit(50, 50)->encode('png', 100)->save($path);
        }

        Category::query()->create([
            ...$request->toArray(),
            'image' => $filename ?? null
        ]);

        return to_route('staff.categories.index')
            ->with('success', 'Category successfully added');
    }

    /**
     * Category Edit Form.
     */
    public function edit(Category $category): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.category.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update A Category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): \Illuminate\Http\RedirectResponse
    {
        if ($request->image !== null) {
            $filename = 'category-'.uniqid('', true).'.'.$request->image->getClientOriginalExtension();
            $path = Storage::disk('category-images')->path($filename);
            Image::make($request->image->getRealPath())->fit(50, 50)->encode('png', 100)->save($path);

            if ($category->image !== null) {
                Storage::disk('category-images')->delete($category->image);
            }
        }

        $category->update([
            ...$request->toArray(),
            'image' => $filename ?? null
        ]);

        return to_route('staff.categories.index')
            ->with('success', 'Category successfully modified');
    }

    /**
     * Destroy A Category.
     *
     * @throws Exception
     */
    public function destroy(Category $category): \Illuminate\Http\RedirectResponse
    {
        $category->delete();

        return to_route('staff.categories.index')
            ->with('success', 'Category successfully deleted');
    }
}
