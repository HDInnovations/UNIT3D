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
    | Powered By
    |--------------------------------------------------------------------------
    |
    | A string that describes the core software that powers the application
    |
    */

    'powered-by' => 'Powered By UNIT3D v9.0.5',

    /*
    |--------------------------------------------------------------------------
    | Codebase Name
    |--------------------------------------------------------------------------
    |
    | Name of Codebase
    |
    */

    'codebase' => 'UNIT3D',

    /*
    |--------------------------------------------------------------------------
    | Codebase Version
    |--------------------------------------------------------------------------
    |
    | Version of Codebase
    |
    */

    'version' => 'v9.0.5',

    /*
    |--------------------------------------------------------------------------
    | Owner Account Configuration
    |--------------------------------------------------------------------------
    |
    | Various settings related to the Owner account configuration
    |
    */

    'owner-username'         => env('DEFAULT_OWNER_NAME', 'UNIT3D'),
    'default-owner-email'    => env('DEFAULT_OWNER_EMAIL', 'none@none.com'),
    'default-owner-password' => env('DEFAULT_OWNER_PASSWORD', 'UNIT3D'),

    // If using a Reverse Proxy for HTTPS set the 'PROXY_SCHEME' value in your .env file to `https` or adjust the below value
    'proxy_scheme'      => env('PROXY_SCHEME', false),
    'root_url_override' => env('FORCE_ROOT_URL', false),

    // Global Rate Limit for Comments - X Per Minute
    'comment-rate-limit' => env('COMMENTS_PER_MINUTE', 3),

    /*
    |--------------------------------------------------------------------------
    | External Chat Platform
    |--------------------------------------------------------------------------
    |
    | Settings to configure an external chat platform
    |
    */

    'chat-link-name' => 'Discord',
    'chat-link-icon' => 'fab fa-discord',
    'chat-link-url'  => '',
];
