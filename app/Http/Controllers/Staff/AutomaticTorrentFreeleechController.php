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
use App\Http\Requests\Staff\StoreAutomaticTorrentFreeleechRequest;
use App\Http\Requests\Staff\UpdateAutomaticTorrentFreeleechRequest;
use App\Models\AutomaticTorrentFreeleech;
use App\Models\Category;
use App\Models\Resolution;
use App\Models\Type;

class AutomaticTorrentFreeleechController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.automatic-torrent-freeleech.index', [
            'automaticTorrentFreeleeches' => AutomaticTorrentFreeleech::orderby('position')->get(),
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.automatic-torrent-freeleech.create', [
            'categories'  => Category::orderBy('position')->get(),
            'resolutions' => Resolution::orderBy('position')->get(),
            'types'       => Type::orderBy('position')->get(),
        ]);
    }

    public function store(StoreAutomaticTorrentFreeleechRequest $request): \Illuminate\Http\RedirectResponse
    {
        AutomaticTorrentFreeleech::create($request->validated());

        return to_route('staff.automatic_torrent_freeleeches.index')
            ->with('success', 'Resolution successfully added');
    }

    public function edit(AutomaticTorrentFreeleech $automaticTorrentFreeleech): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.automatic-torrent-freeleech.edit', [
            'automaticTorrentFreeleech' => $automaticTorrentFreeleech,
            'categories'                => Category::orderBy('position')->get(),
            'resolutions'               => Resolution::orderBy('position')->get(),
            'types'                     => Type::orderBy('position')->get(),
        ]);
    }

    public function update(UpdateAutomaticTorrentFreeleechRequest $request, AutomaticTorrentFreeleech $automaticTorrentFreeleech): \Illuminate\Http\RedirectResponse
    {
        $automaticTorrentFreeleech->update($request->validated());

        return to_route('staff.automatic_torrent_freeleeches.index')
            ->with('success', 'Resolution successfully modified');
    }

    public function destroy(AutomaticTorrentFreeleech $automaticTorrentFreeleech): \Illuminate\Http\RedirectResponse
    {
        $automaticTorrentFreeleech->delete();

        return to_route('staff.automatic_torrent_freeleeches.index')
            ->with('success', 'Resolution successfully deleted');
    }
}
