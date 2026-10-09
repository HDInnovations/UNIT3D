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
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

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
    public function store(Request $request, TwoFactorAuthenticationProvider $provider): \Illuminate\Http\RedirectResponse
    {
        /** @see https://github.com/laravel/fortify/blob/f7c3fd787a64ada544353c0423e4589b1626ec75/src/Http/Controllers/TwoFactorAuthenticatedSessionController.php#L56 */
        if (
            $request->recovery_code
            && $code = collect($request->user()->recoveryCodes())->first(fn ($code) => hash_equals($code, $request->recovery_code))
        ) {
            $request->user()->replaceRecoveryCode($code);
        } elseif (
            ! $provider->verify(
                Fortify::currentEncrypter()->decrypt($request->user()->two_factor_secret),
                $request->code
            )
        ) {
            throw ValidationException::withMessages([
                'code' => [__('The provided two factor authentication code was invalid.')],
            ])->errorBag('confirmTwoFactorAuthentication');
        }

        $request->session()->put('auth.two_factor_confirmed_at', Date::now()->unix());

        return redirect()->intended();
    }
}
