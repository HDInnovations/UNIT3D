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
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Captcha On/Off
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Type
    |--------------------------------------------------------------------------
    |
    | Captcha system to use: 'hidden' or 'turnstile'
    |     - hidden is a basic captcha that relies on randomly generated strings
    |       and submission timing.
    |
    |     - turnstile is a highly secure captcha that uses Cloudflare Turnstile
    |       bot detection but it requires a site key and secret key to use
    |       https://developers.cloudflare.com/turnstile/get-started/#new-sites
    */

    'type' => env('CAPTCHA_TYPE', 'hidden'),

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY', ''),
        'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    ],
];
