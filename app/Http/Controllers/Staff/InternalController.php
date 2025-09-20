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
use App\Http\Requests\Staff\StoreInternalRequest;
use App\Http\Requests\Staff\UpdateInternalRequest;
use App\Models\Internal;
use App\Models\User;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\GroupControllerTest
 */
class InternalController extends Controller
{
    /**
     * Display All Internal Groups.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.internals.index', [
            'internalGroups' => Internal::orderBy('name')->get(),
            'internalUsers'  => User::with(['group', 'internals'])
                ->withCount('torrents as total_uploads')
                ->whereRelation('group', 'is_internal', '=', true)
                ->orWhereHas('internals')
                // Count recent uploads for current user
                ->withCount(['torrents as recent_uploads' => fn ($query) => $query
                    ->where('created_at', '>', now()->subDays(60))
                ])
                // Count total personal releases for current user
                ->withCount(['torrents as total_personal_releases' => fn ($query) => $query
                    ->where('personal_release', '=', true)
                ])
                // Count recent personal releases for current user
                ->withCount(['torrents as recent_personal_releases' => fn ($query) => $query
                    ->where('personal_release', '=', true)
                    ->where('created_at', '>', now()->subDays(60))
                ])
                // Count total internal releases for current user
                ->withCount(['torrents as total_internal_releases' => fn ($query) => $query
                    ->where('internal', '=', 1)
                ])
                // Count recent internal releases for current user
                ->withCount(['torrents as recent_internal_releases' => fn ($query) => $query
                    ->where('internal', '=', 1)
                    ->where('created_at', '>', now()->subDays(60))
                ])
                ->get(),
        ]);
    }

    /**
     * Edit A group.
     */
    public function edit(Internal $internal): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('Staff.internals.edit', [
            'internal' => $internal->load([
                'users' => fn ($query) => $query->with('group')->orderByPivot('position', 'asc'),
            ]),
        ]);
    }

    /**
     * Save a group change.
     */
    public function update(UpdateInternalRequest $request, Internal $internal): \Illuminate\Http\RedirectResponse
    {
        $internal->update($request->validated());

        return to_route('staff.internals.index')
            ->with('success', 'Internal group was updated successfully!');
    }

    /**
     * Internal Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.internals.create');
    }

    /**
     * Store A New Internal Group.
     */
    public function store(StoreInternalRequest $request): \Illuminate\Http\RedirectResponse
    {
        Internal::create($request->validated());

        return to_route('staff.internals.index')
            ->with('success', 'New internal group added!');
    }

    /**
     * Delete A Internal Group.
     */
    public function destroy(Internal $internal): \Illuminate\Http\RedirectResponse
    {
        $internal->delete();

        return to_route('staff.internals.index')
            ->with('success', 'Group has been removed.');
    }
}
