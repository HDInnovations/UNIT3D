<?php

declare(strict_types=1);

return [
    'metrics_enabled' => env('CAPACITY_METRICS_ENABLED', env('APP_ENV') !== 'testing'),
    'fpm_host'        => env('CAPACITY_FPM_HOST', '127.0.0.1'),
    'fpm_ports'       => ['web' => 9100, 'tracker' => 9101],

    // GET /health/tracker (PVE healthcheck). Empty token disables the route.
    'health' => [
        'token'                 => env('TRACKER_HEALTH_TOKEN', ''),
        'max_queue_age_seconds' => (int) env('TRACKER_HEALTH_MAX_QUEUE_AGE', 60),
        'max_flush_age_seconds' => (int) env('TRACKER_HEALTH_MAX_FLUSH_AGE', 120),
    ],
];
