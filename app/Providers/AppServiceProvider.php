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

use App\Helpers\ByteUnits;
use App\Helpers\HiddenCaptcha;
use App\Http\Middleware\BlockIpAddress;
use App\Http\Middleware\CheckApiScope;
use App\Http\Middleware\CheckForAdmin;
use App\Http\Middleware\CheckForModo;
use App\Http\Middleware\CheckForOwner;
use App\Http\Middleware\CheckIfBanned;
use App\Http\Middleware\ConfirmTwoFactor;
use App\Http\Middleware\SetLanguage;
use App\Http\Middleware\UpdateLastAction;
use App\Interfaces\ByteUnitsInterface;
use App\Models\Apikey;
use App\Models\User;
use App\Observers\UserObserver;
use App\View\Composers\FooterComposer;
use App\View\Composers\TopNavComposer;
use HDVinnie\SecureHeaders\SecureHeaders;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     */
    #[Override]
    public function register(): void
    {
        // Hidden Captcha
        $this->app->bind('hiddencaptcha', HiddenCaptcha::class);

        // Gabrielelana byte-units
        $this->app->bind(ByteUnitsInterface::class, ByteUnits::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        // User Observer For Cache
        User::observe(UserObserver::class);

        // Hidden Captcha
        Blade::directive('hiddencaptcha', fn ($mustBeEmptyField = '_username') => \sprintf('<?= App\Helpers\HiddenCaptcha::render(%s); ?>', $mustBeEmptyField));

        // BBcode
        Blade::directive('bbcode', fn (?string $bbcodeString) => "<?php echo (new \hdvinnie\LaravelJoyPixels\LaravelJoyPixels())->toImage((new \App\Helpers\Linkify())->linky((new \App\Helpers\Bbcode())->parse({$bbcodeString}))); ?>");

        // Linkify
        Blade::directive('linkify', fn (?string $contentString) => "<?php echo (new \App\Helpers\Linkify)->linky(e({$contentString})); ?>");

        $this->app['validator']->extendImplicit(
            'hiddencaptcha',
            function ($_attribute, $_value, $parameters, $validator) {
                $minLimit = (isset($parameters[0]) && is_numeric($parameters[0])) ? $parameters[0] : 0;
                $maxLimit = (isset($parameters[1]) && is_numeric($parameters[1])) ? $parameters[1] : 1_200;

                if (!HiddenCaptcha::check($validator, $minLimit, $maxLimit)) {
                    $validator->setCustomMessages(['hiddencaptcha' => 'Captcha error']);

                    return false;
                }

                return true;
            }
        );

        // Add attributes to vite scripts and styles
        Vite::useScriptTagAttributes([
            'crossorigin' => 'anonymous',
        ]);

        Vite::useStyleTagAttributes([
            'crossorigin' => 'anonymous',
        ]);

        View::composer('partials.footer', FooterComposer::class);
        View::composer('partials.top-nav', TopNavComposer::class);

        TrimStrings::except([
            'current_password',
            'password',
            'password_confirmation',
            'info_hash',
            'peer_id',
        ]);

        Auth::viaRequest('rsskey', fn (Request $request) => User::query()->where('rsskey', '=', $request->route('rsskey'))->first());

        Auth::viaRequest('apikey', function (Request $request): ?User {
            if (!$request->bearerToken()) {
                return null;
            }

            $apikey = Apikey::query()
                ->where('content', '=', $request->bearerToken())
                ->first();

            if ($apikey === null) {
                return null;
            }

            $apikey->update([
                'last_used_at' => now(),
            ]);

            return $apikey->user;
        });

        Livewire::addPersistentMiddleware([
            BlockIpAddress::class,
            CheckApiScope::class,
            CheckForAdmin::class,
            CheckForModo::class,
            CheckForOwner::class,
            CheckIfBanned::class,
            ConfirmTwoFactor::class,
            SetLanguage::class,
            UpdateLastAction::class,
        ]);

        Vite::useCspNonce(SecureHeaders::nonce());

        Context::add('url', $request->url());
    }
}
