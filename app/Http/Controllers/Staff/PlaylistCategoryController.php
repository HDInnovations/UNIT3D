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
use App\Http\Requests\Staff\StorePlaylistCategoryRequest;
use App\Http\Requests\Staff\UpdatePlaylistCategoryRequest;
use App\Models\Category;
use App\Models\PlaylistCategory;
use Exception;

class PlaylistCategoryController extends Controller
{
    /**
     * Display all playlist categories.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.playlist-category.index', [
            'playlistCategories' => PlaylistCategory::query()->orderBy('position')->get(),
        ]);
    }

    /**
     * Show form for creating a new category.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.playlist-category.create');
    }

    /**
     * Store a category.
     */
    public function store(StorePlaylistCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        PlaylistCategory::query()->create($request->validated());

        return to_route('staff.playlist_categories.index');
    }

    /**
     * Playlist category edit form.
     */
    public function edit(PlaylistCategory $playlistCategory): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.playlist-category.edit', [
            'playlistCategory' => $playlistCategory,
        ]);
    }

    /**
     * Update a playlist category.
     */
    public function update(UpdatePlaylistCategoryRequest $request, PlaylistCategory $playlistCategory): \Illuminate\Http\RedirectResponse
    {
        $playlistCategory->update($request->validated());

        return to_route('staff.playlist_categories.index');
    }

    /**
     * Destroy a category.
     *
     * @throws Exception
     */
    public function destroy(PlaylistCategory $playlistCategory): \Illuminate\Http\RedirectResponse
    {
        if ($playlistCategory->playlists()->exists()) {
            return to_route('staff.playlist_categories.index')
                ->withErrors('Can\'t delete playlist category that still contains playlists');
        }

        $playlistCategory->delete();

        return to_route('staff.playlist_categories.index');
    }
}
