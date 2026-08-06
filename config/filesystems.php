<?php

declare(strict_types=1);

return [
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
    ],
];
