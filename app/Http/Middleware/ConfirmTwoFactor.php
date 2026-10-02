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

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;

class ConfirmTwoFactor
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UrlGenerator $urlGenerator,
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): mixed
    {
        if (
            $request->user()->two_factor_confirmed_at !== null
            && (
                $request->user()->two_factor_confirmed_at < now()->subSeconds(300)
                || $request->session()->get('auth.two_factor_confirmed_at', 0) < now()->subSeconds(300)->unix()
            )
        ) {
            return $this->responseFactory->redirectGuest($this->urlGenerator->route('two-factor.confirm'));
        }

        return $next($request);
    }
}
