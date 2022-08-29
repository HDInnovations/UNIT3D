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

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Internal;
use App\Models\Page;
use Illuminate\Support\Facades\DB;

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

        return \view('page.index', ['pages' => $pages]);
    }

    /**
     * Show A Page.
     */
    public function show(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $page = Page::findOrFail($id);

        return \view('page.page', ['page' => $page]);
    }

    /**
     * Show Staff Page.
     */
    public function staff(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $staff = Group::with('users:id,username,group_id,title')->where('is_modo', '=', 1)->orWhere('is_admin', '=', 1)->get()->sortByDesc('position');

        return \view('page.staff', ['staff' => $staff]);
    }

    /**
     * Show Internals Page.
     */
    public function internal(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $internals = Internal::with('users')->get()->sortBy('name');

        return \view('page.internal', ['internals' => $internals]);
    }

    /**
     * Show Client-Blacklist Page.
     */
    public function clientblacklist(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        //$clients = \config('blacklist.clients', []);
        $clients = DB::table('blacklist_clients')->get();

        return \view('page.blacklist.client', ['clients' => $clients]);
    }

    /**
     * Show Releasegroup-Blacklist Page.
     */
    public function releasegroupblacklist(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $releasegroups = DB::table('blacklist_releasegroups')->get();

        return \view('page.blacklist.releasegroup', ['releasegroups' => $releasegroups]);
    }

    /**
     * Show About Us Page.
     */
    public function about(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return \view('page.aboutus');
    }
}
