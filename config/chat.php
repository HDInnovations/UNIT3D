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
    | Which chatroom should system messages be routed to ?
    |--------------------------------------------------------------------------
    |
    | Note: can use the id or name of the chatroom
    | id (integer) example: 1
    | name (string) example: 'General'
    |
    */

    'system_chatroom' => 'Obecná diskuze',

    /*
    |--------------------------------------------------------------------------
    | Total Messages to keep in history (per chatroom)
    |--------------------------------------------------------------------------
    |
    | Total Messages to keep in history (per chatroom)?
    |
    */

    'message_limit' => 100,

    /*
    |--------------------------------------------------------------------------
    | Nerd Bot
    |--------------------------------------------------------------------------
    |
    | NerdBot On / Off
    |
    */

    'nerd_bot' => true,
];
