<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => true,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw'      => true,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
        ],

        'ftp' => [
            'driver'   => 'ftp',
            'host'     => 'ftp.example.com',
            'username' => 'your-username',
            'password' => 'your-password',

            // Optional FTP Settings...
            // 'port' => 21,
            // 'root' => '',
            // 'passive' => true,
            // 'ssl' => true,
            // 'timeout' => 30,
        ],

        'sftp' => [
            'driver'   => 'sftp',
            'host'     => 'example.com',
            'username' => 'your-username',
            'password' => 'your-password',

            // Settings for SSH key based authentication...
            'privateKey' => '/path/to/privateKey',
            'passphrase' => 'encryption-password',

            // Optional SFTP Settings...
            // 'port' => 22,
            // 'root' => '',
            // 'timeout' => 30,
        ],

        'backups' => [
            'driver' => 'local',
            'root'   => storage_path('backups'),
        ],

        // UNIT3D Custom Disks (Alphabetical Order)
        'article-images' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/articles/images'),
        ],

        'attachment-files' => [
            'driver' => 'local',
            'root'   => storage_path('app/files/attachments/files'),
        ],

        'user-avatars' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/users/avatars'),
        ],

        'user-icons' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/users/icons'),
        ],

        'category-images' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/categories/images'),
        ],

        'playlist-images' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/playlists/images'),
        ],

        'subtitle-files' => [
            'driver' => 'local',
            'root'   => storage_path('app/files/subtitles/files'),
        ],

        'temporary-nfos' => [
            'driver' => 'local',
            'root'   => storage_path('app/tmp/nfos'),
        ],

        'torrent-banners' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/torrents/banners'),
        ],

        'torrent-covers' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/torrents/covers'),
        ],

        'torrent-files' => [
            'driver' => 'local',
            'root'   => storage_path('app/files/torrents/files'),
        ],
        'collectible-icons' => [
            'driver' => 'local',
            'root'   => storage_path('app/images/collectibles/icons'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
