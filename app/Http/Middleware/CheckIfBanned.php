<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\Group;
use Closure;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure                 $next
     * @param string|null              $guard
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next, ?string $guard = null)
    {
        $user = $request->user();
        $banned_group = cache()->rememberForever('banned_group', function () {
            return Group::where('slug', '=', 'banned')->pluck('id');
        });

        if ($user && $user->group_id == $banned_group[0]) {
            auth()->logout();
            $request->session()->flush();

            return redirect()->route('login')
                ->withErrors('This account is Banned!');
        }

        return $next($request);
    }
}
