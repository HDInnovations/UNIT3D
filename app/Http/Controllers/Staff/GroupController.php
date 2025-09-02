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
use App\Http\Requests\Staff\StoreGroupRequest;
use App\Http\Requests\Staff\UpdateGroupRequest;
use App\Models\ForumCategory;
use App\Models\Group;
use App\Models\User;
use App\Services\Unit3dAnnounce;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * @see \Tests\Feature\Http\Controllers\Staff\GroupControllerTest
 */
class GroupController extends Controller
{
    /**
     * Display All Groups.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.group.index', [
            'groups' => Group::orderBy('position')->get(),
        ]);
    }

    /**
     * Group Add Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.group.create', [
            'forumCategories' => ForumCategory::query()
                ->with([
                    'forums' => fn ($query) => $query->orderBy('position')
                ])
                ->orderBy('position')
                ->get(),
        ]);
    }

    /**
     * Store A New Group.
     */
    public function store(StoreGroupRequest $request): \Illuminate\Http\RedirectResponse
    {
        $group = Group::create(['slug' => Str::slug($request->validated('group.name'))] + $request->validated('group'));

        $group->permissions()->upsert($request->validated('permissions'), ['forum_id', 'group_id']);

        Unit3dAnnounce::addGroup($group);

        return to_route('staff.groups.index')
            ->with('success', 'Group Was Created Successfully!');
    }

    /**
     * Group Edit Form.
     */
    public function edit(Group $group): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.group.edit', [
            'group'           => $group,
            'forumCategories' => ForumCategory::query()
                ->with([
                    'forums' => fn ($query) => $query->orderBy('position')
                ])
                ->orderBy('position')
                ->get(),
        ]);
    }

    /**
     * Edit A Group.
     */
    public function update(UpdateGroupRequest $request, Group $group): \Illuminate\Http\RedirectResponse
    {
        $group->update(['slug' => Str::slug($request->validated('group.name'))] + $request->validated('group'));

        $group->permissions()->upsert($request->validated('permissions'), ['forum_id', 'group_id']);

        cache()->forget('group:'.$group->id);

        Unit3dAnnounce::addGroup($group);

        return to_route('staff.groups.index')
            ->with('success', 'Group Was Updated Successfully!');
    }

    /**
     * Delete A Group.
     */
    public function destroy(Group $group): \Illuminate\Http\RedirectResponse
    {
        if ($group->system_required) {
            return to_route('staff.groups.index')
                ->with('error', 'Cannot delete system required group: '.$group->name);
        }

        if ($group->users()->exists()) {
            $defaultGroup = Group::query()
                ->where('system_required', '=', true)
                ->firstWhere('name', '=', 'User');

            if (!$defaultGroup) {
                return to_route('staff.groups.index')
                    ->with('error', 'Cannot find default User group to reassign users.');
            }

            // Get user count before reassignment
            $userCount = $group->users()->count();

            // Move all users to the default User group using the relationship
            $group->users()->update(['group_id' => $defaultGroup->id]);

            // Run auto:group command to reassign users to appropriate groups
            Artisan::call('auto:group');

            return to_route('staff.groups.index')
                ->with('success', "Group \"{$group->name}\" was deleted successfully. {$userCount} users were moved to their appropriate groups.");
        }

        Unit3dAnnounce::removeGroup($group);
        $group->delete();

        return to_route('staff.groups.index')
            ->with('success', "Group \"{$group->name}\" was deleted successfully.");
    }
}
