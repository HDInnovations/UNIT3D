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

namespace App\Actions\Fortify;

use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable as FortifyRedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;

class RedirectIfTwoFactorAuthenticatable extends FortifyRedirectIfTwoFactorAuthenticatable
{
    /**
     * Handle the incoming request.
     *
     * Extended to also check for WebAuthn security keys.
     */
    public function handle($request, $next): mixed
    {
        $user = $this->validateCredentials($request);

        if ($user !== null && \is_object($user) && method_exists($user, 'webauthnKeys') && $user->webauthnKeys()->exists()) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_secret !== null
                && optional($user)->two_factor_confirmed_at !== null
                && \in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user), true)) {
                return $this->twoFactorChallengeResponse($request, $user);
            }

            return $next($request);
        }

        if (optional($user)->two_factor_secret !== null
            && \in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user), true)) {
            return $this->twoFactorChallengeResponse($request, $user);
        }

        return $next($request);
    }
}
