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

namespace App\Http\Requests\Staff;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class UpdateGroupRequest extends Data
{
    public function __construct(
        public UpdateGroupGroupRequest $group,
        /** @var Collection<int, UpdateGroupPermissionRequest> */
        public Collection $permissions,
    ) {
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public static function authorize(#[RouteParameter('group')] Group $group, #[CurrentUser] User $currentUser, Request $request): bool
    {
        return $currentUser->group->is_owner || (
            $group->level < $currentUser->group->level
            && $request->integer('group.level') < $currentUser->group->level
            && !$request->integer('group.is_owner')
            && !$group->is_owner
        );
    }
}
