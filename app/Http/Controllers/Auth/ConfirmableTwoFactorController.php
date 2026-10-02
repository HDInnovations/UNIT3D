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

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;

class ConfirmableTwoFactorController extends Controller
{
    /**
     * Show the confirm two factor view.
     */
    public function show(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('auth.confirm-two-factor');
    }

    /**
     * Confirm the user's two factor code.
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm): \Illuminate\Http\RedirectResponse
    {
        $confirm($request->user(), $request->input('code'));

        $request->session()->put('auth.two_factor_confirmed_at', Date::now()->unix());

        return redirect()->intended();
    }
}
