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
    | External tracker
    |--------------------------------------------------------------------------
    |
    | Configure site to use UNIT3D-Announce instead of built-in tracker
    |
    */
    'external_tracker' => [
        /*
        |--------------------------------------------------------------------------
        | External tracker
        |--------------------------------------------------------------------------
        |
        | Enable external tracker
        |
        */

        'is_enabled' => false,

        /*
        |--------------------------------------------------------------------------
        | External Tracker Host IP
        |--------------------------------------------------------------------------
        |
        | IP Address of External Tracker. Should be a local IP Address.
        |
        */

        'host' => env('TRACKER_HOST'),

        /*
        |--------------------------------------------------------------------------
        | External Tracker Port
        |--------------------------------------------------------------------------
        |
        | Port of External Tracker.
        |
        */

        'port' => env('TRACKER_PORT'),

        /*
        |--------------------------------------------------------------------------
        | External Tracker Unix Domain Socket
        |--------------------------------------------------------------------------
        |
        | Path to unix domain socket of external tracker. Can be used in place of
        | the host and port.
        |
        */

        'unix_socket' => env('TRACKER_UNIX_SOCKET'),

        /*
        |--------------------------------------------------------------------------
        | External Tracker API Key
        |--------------------------------------------------------------------------
        |
        | API Key of External Tracker IP. Should be a local IP.
        |
        */

        'key' => env('TRACKER_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limit
    |--------------------------------------------------------------------------
    |
    | Amount Of Locations A User Can Seed A Single Torrent From
    |
    */

    'rate_limit' => 3,

    /*
    |--------------------------------------------------------------------------
    | Client Connectable Check
    |--------------------------------------------------------------------------
    |
    | This option toggles Client connectivity check
    | !!! Attention: Will result in leaking the server IP !!!
    | It will result in higher disc / DB IO
    |
    */

    'connectable_check' => false,

    /*
    |--------------------------------------------------------------------------
    | Connectable check interval
    |--------------------------------------------------------------------------
    |
    | Amount Of Time until the next connectable check
    |
    */
    'connectable_check_interval' => 60 * 30,

    /*
    |--------------------------------------------------------------------------
    | Download Slots System
    |--------------------------------------------------------------------------
    |
    | Enables download slots for user groups set in group settings via staff dashboard
    | Make sure you have a slot value set for EVERY group before enabling. This system is disabled
    | by default and groups download_slots are null. Null equals unlimited slots. Groups like banned should be
    | set to 0
    |
    */

    'slots_system' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Log all torrent announces and show in staff dashboard
    | Used mainly for debugging purposes - Will generate significant amounts of data
    |
    */

    'log_announces' => false,
];
