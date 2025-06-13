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
use App\Interfaces\ByteUnitsInterface;
use App\Models\User;
use App\Observers\UserObserver;
use App\View\Composers\FooterComposer;
use App\View\Composers\TopNavComposer;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     */
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
    public function boot(): void
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
            function ($attribute, $value, $parameters, $validator) {
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

        // Scramble API docs configuration
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->components->securitySchemes['apikey'] = SecurityScheme::apiKey('query', 'api_token');
                $openApi->components->securitySchemes['bearer'] = SecurityScheme::http('bearer');

                $openApi->security[] = new SecurityRequirement([
                    'apikey' => [],
                    'bearer' => [],
                ]);
            });
    }
}
