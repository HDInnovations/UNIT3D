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

use App\Models\Seedbox;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\SeedboxControllerTest
 */
class SeedboxController extends Controller
{
    /**
     * Get A Users Registered Seedboxes.
     *
     * @param \App\Models\User $username
     */
    public function index(Request $request, $username): \Illuminate\Contracts\View\Factory | \Illuminate\View\View
    {
        $user = User::where('username', '=', $username)->firstOrFail();

        \abort_unless(($request->user()->group->is_modo || $request->user()->id == $user->id), 403);

        $seedboxes = Seedbox::where('user_id', '=', $user->id)->paginate(25);

        return \view('seedbox.index', ['user' => $user, 'seedboxes' => $seedboxes]);
    }

    /**
     * Store A Seedbox.
     *
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function store(Request $request)
    {
        $user = $request->user();

        $seedbox = new Seedbox();
        $seedbox->user_id = $user->id;
        $seedbox->name = $request->input('name');
        $seedbox->ip = $request->input('ip');

        $v = \validator($seedbox->toArray(), [
            'name'  => 'required|alpha_num',
            'ip'    => 'required|unique:clients,ip',
        ]);

        if ($v->fails()) {
            return \redirect()->route('seedboxes.index', ['username' => $user->username])
                ->withErrors($v->errors());
        }
        $seedbox->save();

        return \redirect()->route('seedboxes.index', ['username' => $user->username])
            ->withSuccess('Seedbox Has Been Successfully Added!');
    }

    /**
     * Delete A Seedbox.
     *
     * @param \App\Models\Seedbox $id
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function destroy(Request $request, $id)
    {
        $user = $request->user();
        $seedbox = Seedbox::findOrFail($id);

        \abort_unless(($user->group->is_modo || $user->id == $seedbox->user_id), 403);

        $seedbox->delete();

        return \redirect()->route('seedboxes.index', ['username' => $user->username])
            ->withSuccess('Seedbox Has Been Successfully Deleted');
    }
}
