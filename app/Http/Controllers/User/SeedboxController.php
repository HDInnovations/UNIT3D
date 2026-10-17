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

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Seedbox;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\SeedboxControllerTest
 */
class SeedboxController extends Controller
{
    /**
     * Get A Users Registered Seedboxes.
     */
    public function index(Request $request, User $user): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        abort_unless(($request->user()->group->is_modo || $request->user()->is($user)), 403);

        return view('user.seedbox.index', [
            'user'      => $user,
            'seedboxes' => $user->seedboxes()->paginate(25),
        ]);
    }

    /**
     * Store A Seedbox.
     */
    protected function store(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->is($user), 403);

        // The user's seedbox IPs are encrypted, so they have to be decrypted first to check that the new IP inputted is unique
        $userSeedboxes = Seedbox::query()->where('user_id', '=', $user->id)->get(['ip', 'name']);
        $seedboxIps = $userSeedboxes->pluck('ip')->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP) !== false);
        $seedboxNames = $userSeedboxes->pluck('name');

        $request->validate(
            [
                'name' => [
                    'required',
                    'alpha_num',
                    Rule::notIn($seedboxNames),
                ],
                'ip' => [
                    'bail',
                    'required',
                    'ip',
                    Rule::notIn($seedboxIps),
                ],
            ],
            [
                'name.not_in' => 'You have already used this seedbox name.',
                'ip.not_in'   => 'You have already registered this seedbox IP.',
            ]
        );

        Seedbox::query()->create([
            'user_id' => $user->id,
            'name'    => $request->name,
            'ip'      => $request->ip,
        ]);

        return to_route('users.seedboxes.index', ['user' => $user])
            ->with('success', trans('user.seedbox-added-success'));
    }

    /**
     * Delete A Seedbox.
     *
     * @throws Exception
     */
    protected function destroy(Request $request, User $user, Seedbox $seedbox): \Illuminate\Http\RedirectResponse
    {
        abort_unless($user->group->is_modo || $request->user()->is($user), 403);

        $seedbox->delete();

        return to_route('users.seedboxes.index', ['user' => $user])
            ->with('success', trans('user.seedbox-deleted-success'));
    }
}
