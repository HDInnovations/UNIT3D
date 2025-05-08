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

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    final public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->removeIndexPhpFromUrl();

        $this->routes(function (): void {
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
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('web', fn (Request $request): Limit => $request->user()
            ? Limit::perMinute(
                cache()->remember(
                    'group:'.$request->user()->group_id.':is_modo',
                    5,
                    fn () => $request->user()->group()->value('is_modo')
                )
                    ? 60
                    : 30
            )
                ->by('web'.$request->user()->id)
            : Limit::perMinute(8)->by('web'.$request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(30)->by('api'.$request->ip()));
        RateLimiter::for('announce', fn (Request $request) => Limit::perMinute(500)->by('announce'.$request->ip()));
        RateLimiter::for('chat', fn (Request $request) => Limit::perMinute(60)->by('chat'.($request->user()?->id ?? $request->ip())));
        RateLimiter::for('rss', fn (Request $request) => Limit::perMinute(30)->by('rss'.$request->ip()));
        RateLimiter::for('authenticated-images', fn (Request $request): Limit => Limit::perMinute(200)->by('authenticated-images:'.$request->user()->id));
        RateLimiter::for('search', fn (Request $request): Limit => Limit::perMinute(100)->by('search:'.$request->user()->id));
        RateLimiter::for('tmdb', fn (): Limit => Limit::perSecond(2));
    }

    protected function removeIndexPhpFromUrl(): void
    {
        if (str_contains(request()->getRequestUri(), '/index.php/')) {
            $url = str_replace('index.php/', '', request()->getRequestUri());

            if ($url !== '') {
                header("Location: {$url}", true, 301);

                exit;
            }
        }
    }
}
