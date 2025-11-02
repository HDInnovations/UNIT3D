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
    | IRC Bot External
    |--------------------------------------------------------------------------
    |
    | IRC Bot External Settings
    |
    */

    'is_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Host
    |--------------------------------------------------------------------------
    |
    | Host takes an ip/localhost as http endpoint.
    |
    */

    'host' => env('IRC_ANNOUNCE_EXTERNAL_HOST'),

    /*
    |--------------------------------------------------------------------------
    | Port
    |--------------------------------------------------------------------------
    |
    | Port for the external announce service
    |
    */

    'port' => env('IRC_ANNOUNCE_EXTERNAL_PORT'),

    /*
    |--------------------------------------------------------------------------
    | Unix socket
    |--------------------------------------------------------------------------
    |
    | Path to unix domain socket for external announce service, like /path/to/file.sock
    |
    */

    'unix_socket' => env('IRC_ANNOUNCE_EXTERNAL_UNIX_SOCKET'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | API Key for the external announce service. Not required for unix sockets, but recommended.
    |
    */

    'key' => env('IRC_ANNOUNCE_EXTERNAL_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Channel
    |--------------------------------------------------------------------------
    |
    | Channel to announce torrents to
    |
    */
    'channel' => env('IRC_ANNOUNCE_EXTERNAL_CHANNEL'),
];
