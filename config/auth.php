<?php

declare(strict_types=1);

use App\Enums\AuthGuard;

return [

    'defaults' => [
        'guard'     => AuthGuard::WEB->value,
        'passwords' => 'users',
    ],

    'guards' => [
        AuthGuard::WEB->value => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        AuthGuard::API->value => [
            'driver'   => 'apikey',
            'provider' => 'users',
            'hash'     => false,
        ],

        'rss' => [
            'driver' => 'rsskey',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'cache-user',
            'model'  => App\Models\User::class,
        ],
    ],

    'verification' => [
        'expire' => 1440,
    ],

];
