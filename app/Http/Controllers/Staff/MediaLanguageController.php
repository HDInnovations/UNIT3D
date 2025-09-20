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
use App\Http\Requests\Staff\StoreMediaLanguageRequest;
use App\Http\Requests\Staff\UpdateMediaLanguageRequest;
use App\Models\MediaLanguage;
use Exception;

class MediaLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.media-language.index', [
            'media_languages' => MediaLanguage::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.media-language.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaLanguageRequest $request): \Illuminate\Http\RedirectResponse
    {
        MediaLanguage::create($request->validated());

        return to_route('staff.media_languages.index')
            ->with('success', 'Media language successfully added');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MediaLanguage $mediaLanguage): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.media-language.edit', [
            'media_language' => $mediaLanguage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaLanguageRequest $request, MediaLanguage $mediaLanguage): \Illuminate\Http\RedirectResponse
    {
        $mediaLanguage->update($request->validated());

        return to_route('staff.media_languages.index')
            ->with('success', 'Media language successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(MediaLanguage $mediaLanguage): \Illuminate\Http\RedirectResponse
    {
        $mediaLanguage->delete();

        return to_route('staff.media_languages.index')
            ->with('success', 'Media language has successfully been deleted');
    }
}
