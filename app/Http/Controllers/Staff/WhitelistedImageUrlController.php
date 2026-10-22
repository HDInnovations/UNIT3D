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
use App\Http\Requests\Staff\StoreWhitelistedImageUrlRequest;
use App\Http\Requests\Staff\UpdateWhitelistedImageUrlRequest;
use App\Models\WhitelistedImageUrl;

class WhitelistedImageUrlController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.whitelisted-image-url.index', [
            'whitelistedImageUrls' => WhitelistedImageUrl::query()->orderBy('pattern')->get(),
        ]);
    }

    public function update(UpdateWhitelistedImageUrlRequest $request, WhitelistedImageUrl $whitelistedImageUrl): \Illuminate\Http\RedirectResponse
    {
        $whitelistedImageUrl->update($request->validated());

        cache()->forget('whitelisted-image-urls');

        return to_route('staff.whitelisted_image_urls.index')
            ->with('success', 'Image url pattern updated successfully.');
    }

    public function store(StoreWhitelistedImageUrlRequest $request): \Illuminate\Http\RedirectResponse
    {
        WhitelistedImageUrl::query()->create($request->validated());

        cache()->forget('whitelisted-image-urls');

        return to_route('staff.whitelisted_image_urls.index')
            ->with('success', 'New image url pattern whitelisted.');
    }

    public function destroy(WhitelistedImageUrl $whitelistedImageUrl): \Illuminate\Http\RedirectResponse
    {
        $whitelistedImageUrl->delete();

        cache()->forget('whitelisted-image-urls');

        return to_route('staff.whitelisted_image_urls.index')
            ->with('success', 'Image url pattern removed from whitelist.');
    }
}
