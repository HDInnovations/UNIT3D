<?php

declare(strict_types=1);

return [
    /*
     * Package Service Providers...
     */
    Assada\Achievements\AchievementsServiceProvider::class,
    Spatie\CookieConsent\CookieConsentServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
];
