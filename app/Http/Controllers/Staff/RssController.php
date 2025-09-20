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
use App\Http\Requests\Staff\StoreRssRequest;
use App\Http\Requests\Staff\UpdateRssRequest;
use App\Models\Category;
use App\Models\TmdbGenre;
use App\Models\Resolution;
use App\Models\Rss;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\RssControllerTest
 */
class RssController extends Controller
{
    /**
     * Display a listing of the RSS resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('Staff.rss.index', [
            'public_rss' => Rss::where('is_private', '=', false)->oldest('position')->get(),
        ]);
    }

    /**
     * Show the form for creating a new RSS resource.
     */
    public function create(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.rss.create', [
            'categories'  => Category::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'types'       => Type::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'resolutions' => Resolution::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'genres'      => TmdbGenre::orderBy('name')->get(),
            'user'        => $request->user(),
        ]);
    }

    /**
     * Store a newly created RSS resource in storage.
     */
    public function store(StoreRssRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        $rss = new Rss();
        $rss->name = $request->name;
        $rss->user_id = $request->user()->id;
        $rss->json_torrent = array_merge($rss->expected_fields, $request->validated());
        $rss->is_private = false;
        $rss->position = $request->position;
        $rss->save();

        return to_route('staff.rss.index')
            ->with('success', 'Public RSS feed created');
    }

    /**
     * Show the form for editing the specified RSS resource.
     */
    public function edit(Request $request, Rss $rss): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_if($rss->is_private, 403);

        return view('Staff.rss.edit', [
            'categories'  => Category::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'types'       => Type::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'resolutions' => Resolution::select(['id', 'name', 'position'])->orderBy('position')->get(),
            'genres'      => TmdbGenre::orderBy('name')->get(),
            'user'        => $request->user(),
            'rss'         => $rss,
        ]);
    }

    /**
     * Update the specified RSS resource in storage.
     */
    public function update(UpdateRssRequest $request, Rss $rss): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        abort_if($rss->is_private, 403);

        $rss->update([
            'json_torrent' => array_merge($rss->json_torrent, $rss->expected_fields, $request->validated()),
            'name'         => $request->name,
            'position'     => $request->position,
        ]);

        return to_route('staff.rss.index')
            ->with('success', 'Public RSS feed updated');
    }

    /**
     * Remove the specified RSS resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Rss $rss): \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
    {
        abort_if($rss->is_private, 403);

        $rss->delete();

        return to_route('staff.rss.index')
            ->with('success', 'RSS Feed Deleted!');
    }
}
