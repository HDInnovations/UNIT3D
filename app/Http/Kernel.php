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

namespace App\Http;

use App\Enums\GlobalRateLimit;
use App\Enums\MiddlewareGroup;
use App\Http\Middleware\BlockIpAddress;
use App\Http\Middleware\UpdateLastAction;
use HDVinnie\SecureHeaders\SecureHeadersMiddleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Default Laravel
        InvokeDeferredCallbacks::class,
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        //\App\Http\Middleware\TrustProxies::class,
        HandleCors::class,
        BlockIpAddress::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        MiddlewareGroup::WEB->value => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            SubstituteBindings::class,
            VerifyCsrfToken::class,
            UpdateLastAction::class,
            SecureHeadersMiddleware::class,
            ThrottleRequestsWithRedis::class.':'.GlobalRateLimit::WEB->value,
        ],
        MiddlewareGroup::CHAT->value => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            SubstituteBindings::class,
            VerifyCsrfToken::class,
            UpdateLastAction::class,
            SecureHeadersMiddleware::class,
            ThrottleRequestsWithRedis::class.':'.GlobalRateLimit::CHAT->value,
        ],
        MiddlewareGroup::API->value => [
            ThrottleRequestsWithRedis::class.':'.GlobalRateLimit::API->value,
        ],
        MiddlewareGroup::ANNOUNCE->value => [
            ThrottleRequestsWithRedis::class.':'.GlobalRateLimit::ANNOUNCE->value,
        ],
        MiddlewareGroup::RSS->value => [
            ThrottleRequestsWithRedis::class.':'.GlobalRateLimit::RSS->value,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string>
     */
    protected $middlewareAliases = [
        'guest'    => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequestsWithRedis::class,
    ];
}
