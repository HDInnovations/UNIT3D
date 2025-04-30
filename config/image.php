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

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | Avatar Settings
    |--------------------------------------------------------------------------
    |
    | GIF Avatar Settings
    | Size: "bytes"
    */

    'max_upload_size' => '2000000',

    /*
    |--------------------------------------------------------------------------
    | Remote Image Handling (Torrent Uploads: no_meta or music_meta)
    |--------------------------------------------------------------------------
    |
    | Determines whether to save remote images to the server or use the provided URL.
    | Options: 'save_to_server', 'use_url', 'whitelist_or_save'
    | - 'save_to_server': Always save images to server.
    | - 'use_url': Always use the provided URL.
    | - 'whitelist_or_save': Use URL if it matches a whitelisted pattern in WhitelistedImageUrl, otherwise save to server.
    |
    */
    'remote_image_handling' => 'whitelist_or_save',
];
