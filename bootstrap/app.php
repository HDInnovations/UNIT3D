<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

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

return Application::configure(basePath: \dirname(__DIR__))
    ->withRouting(
        using: function (): void {
            Route::prefix('api')
                ->middleware(['chat'])
                ->group(base_path('routes/vue.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::prefix('announce')
                ->middleware('announce')
                ->group(base_path('routes/announce.php'));

            Route::middleware('rss')
                ->group(base_path('routes/rss.php'));
        },
    )
    ->withCommands([base_path('routes/console.php')])
    ->withBroadcasting(base_path('routes/channels.php'), [
        'middleware' => ['auth', 'verified', 'banned']
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            // Default Laravel
            Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            //\App\Http\Middleware\TrustProxies::class,
            Illuminate\Http\Middleware\HandleCors::class,
            App\Http\Middleware\BlockIpAddress::class,
        ]);

        $middleware->group('web', [
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\Session\Middleware\AuthenticateSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            App\Http\Middleware\UpdateLastAction::class,
            HDVinnie\SecureHeaders\SecureHeadersMiddleware::class,
            'throttle:web',
        ]);

        $middleware->group('chat', [
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\Session\Middleware\AuthenticateSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            App\Http\Middleware\UpdateLastAction::class,
            HDVinnie\SecureHeaders\SecureHeadersMiddleware::class,
            'throttle:chat',
        ]);

        $middleware->group('api', [
            'throttle:api',
        ]);

        $middleware->group('announce', [
            'throttle:announce',
        ]);

        $middleware->group('rss', [
            'throttle:rss',
        ]);

        $middleware->alias([
            'admin'            => App\Http\Middleware\CheckForAdmin::class,
            'auth'             => Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic'       => Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'banned'           => App\Http\Middleware\CheckIfBanned::class,
            'bindings'         => Illuminate\Routing\Middleware\SubstituteBindings::class,
            'cache.headers'    => Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'              => Illuminate\Auth\Middleware\Authorize::class,
            'csrf'             => Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            'guest'            => Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'language'         => App\Http\Middleware\SetLanguage::class,
            'modo'             => App\Http\Middleware\CheckForModo::class,
            'owner'            => App\Http\Middleware\CheckForOwner::class,
            'throttle'         => Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            'signed'           => Illuminate\Routing\Middleware\ValidateSignature::class,
            'verified'         => Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'password.confirm' => Illuminate\Auth\Middleware\RequirePassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            Illuminate\Queue\MaxAttemptsExceededException::class,
            App\Exceptions\MetaFetchNotFoundException::class,
        ]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
