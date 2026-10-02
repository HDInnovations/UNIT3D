<?php

declare(strict_types=1);

return [
    'metrics_enabled' => env('CAPACITY_METRICS_ENABLED', env('APP_ENV') !== 'testing'),
    'fpm_host'        => env('CAPACITY_FPM_HOST', '127.0.0.1'),
    'fpm_ports'       => ['web' => 9100, 'tracker' => 9101],
];
