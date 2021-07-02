<?php
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
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\PageControllerTest
 */
class PageController extends Controller
{
    /**
     * Display All Pages.
     */
    public function index(): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        $pages = Page::all();

        return \view('Staff.page.index', ['pages' => $pages]);
    }

    /**
     * Page Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        return \view('Staff.page.create');
    }

    /**
     * Store A New Page.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $page = new Page();
        $page->name = $request->input('name');
        $page->slug = Str::slug($page->name);
        $page->content = $request->input('content');

        $v = \validator($page->toArray(), [
            'name'    => 'required',
            'slug'    => 'required',
            'content' => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('staff.pages.index')
                ->withErrors($v->errors());
        }
        $page->save();

        return \redirect()->route('staff.pages.index')
            ->withSuccess('Page has been created successfully');
    }

    /**
     * Page Edit Form.
     *
     * @param \App\Models\Page $id
     */
    public function edit($id): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        $page = Page::findOrFail($id);

        return \view('Staff.page.edit', ['page' => $page]);
    }

    /**
     * Edit A Page.
     *
     * @param \App\Models\Page $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $page->name = $request->input('name');
        $page->slug = Str::slug($page->name);
        $page->content = $request->input('content');

        $v = \validator($page->toArray(), [
            'name'    => 'required',
            'slug'    => 'required',
            'content' => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('staff.pages.index')
                ->withErrors($v->errors());
        }
        $page->save();

        return \redirect()->route('staff.pages.index')
            ->withSuccess('Page has been edited successfully');
    }

    /**
     * Delete A Page.
     *
     * @param \App\Models\Page $id
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Page::findOrFail($id)->delete();

        return \redirect()->route('staff.pages.index')
            ->withSuccess('Page has been deleted successfully');
    }
}
