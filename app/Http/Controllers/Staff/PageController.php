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
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $pages = Page::all();

        return \view('Staff.page.index', ['pages' => $pages]);
    }

    /**
     * Page Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return \view('Staff.page.create');
    }

    /**
     * Store A New Page.
     *
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
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
     */
    public function edit(\App\Models\Page $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $page = Page::findOrFail($id);

        return \view('Staff.page.edit', ['page' => $page]);
    }

    /**
     * Edit A Page.
     *
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function update(Request $request, \App\Models\Page $id)
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
     *
     * @throws \Exception
     *
     */
    public function destroy(\App\Models\Page $id): \Illuminate\Http\RedirectResponse
    {
        Page::findOrFail($id)->delete();

        return \redirect()->route('staff.pages.index')
            ->withSuccess('Page has been deleted successfully');
    }
}
