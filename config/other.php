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
    | Site title
    |--------------------------------------------------------------------------
    |
    | Title of Site
    |
    */

    'title' => 'UNIT3D',

    /*
    |--------------------------------------------------------------------------
    | Site SubTitle
    |--------------------------------------------------------------------------
    |
    | SubTitle
    |
    */

    'subTitle' => 'Built On Laravel',

    /*
    |--------------------------------------------------------------------------
    | Site email
    |--------------------------------------------------------------------------
    |
    | Email address to send emails
    |
    */

    'email' => env('DEFAULT_OWNER_EMAIL', 'unit3d@none.com'),

    /*
    |--------------------------------------------------------------------------
    | Meta description
    |--------------------------------------------------------------------------
    |
    | Default meta description content
    |
    */

    'meta_description' => 'Built On Laravel',

    /*
    |--------------------------------------------------------------------------
    | Site Birthdate
    |--------------------------------------------------------------------------
    |
    | Date Site Was Born
    |
    */
    'birthdate' => 'December 30th 2017',

    /*
    |--------------------------------------------------------------------------
    | Freeleech State
    |--------------------------------------------------------------------------
    |
    | Global Freeleech
    |
    */
    'freeleech' => false,

    'freeleech_until' => '12/23/2017 3:00 PM EST',

    /*
    |--------------------------------------------------------------------------
    | Double Upload State
    |--------------------------------------------------------------------------
    |
    | Global Double Upload
    |
    */
    'doubleup' => false,

    /*
    |--------------------------------------------------------------------------
    | Refund Torrent Download
    |--------------------------------------------------------------------------
    |
    | Global Refund Download
    |
    */
    'refundable' => false,

    /*
    |--------------------------------------------------------------------------
    | Min Ratio
    |--------------------------------------------------------------------------
    |
    | Minimum Ratio To Download
    |
    */

    'ratio' => 0.4,

    /*
    |--------------------------------------------------------------------------
    | Invite only
    |--------------------------------------------------------------------------
    |
    | Invite System On/Off (Open Reg / Closed)
    | Expire time in days
    |
    | Restricted mode for invites. If set to true, invites will be restricted
    | Exempt these groups from the invite restrictions
    */
    'invite-only'   => true,
    'invite_expire' => 14,

    'invites_restriced' => false,
    'invite_groups'     => [
        'Administrator',
        'Owner',
    ],
    'max_unused_user_invites' => 1,

    /*
    |--------------------------------------------------------------------------
    | Default Users Stats
    |--------------------------------------------------------------------------
    |
    | This will be the upload and download given to new members. (In Bytes!)
    | Default: 50GiB Upload and 1GiB Download
    */
    'default_upload'   => '53687091200',
    'default_download' => '1073741824',

    /*
    |--------------------------------------------------------------------------
    | Default Site Style
    |--------------------------------------------------------------------------
    | 0 = Classic Light Theme
    | 1 = Galactic Theme
    | 2 = Dark Blue Theme
    | 3 = Dark Green Theme
    | 4 = Dark Pink Theme
    | 5 = Dark Purple Theme
    | 6 = Dark Red Theme
    | 7 = Dark Teal Theme
    | 8 = Dark Yellow Theme
    | 9 = Cosmic Void
    | 10 = Nord
    | 11 = Revel
    | 12 = Material Design v3 Light
    */
    'default_style' => 12,

    /*
    |--------------------------------------------------------------------------
    | Default visible homepage blocks
    |--------------------------------------------------------------------------
    */
    'default_news_visible'            => true,
    'default_chat_visible'            => true,
    'default_featured_visible'        => true,
    'default_random_media_visible'    => true,
    'default_poll_visible'            => true,
    'default_top_torrents_visible'    => true,
    'default_top_users_visible'       => true,
    'default_latest_topics_visible'   => true,
    'default_latest_posts_visible'    => true,
    'default_latest_comments_visible' => true,
    'default_online_visible'          => true,

    /*
    |--------------------------------------------------------------------------
    | Default Font Awesome Style
    |--------------------------------------------------------------------------
    | fas = Solid
    | far = Regular
    | fal = Light
    */
    'font-awesome' => 'fas',

    /*
    |--------------------------------------------------------------------------
    | Application Signups
    |--------------------------------------------------------------------------
    | True/1 = Enabled
    | False/0 = Disabled
    */
    'application_signups' => false,

    /*
    |--------------------------------------------------------------------------
    | Rules Page URL
    |--------------------------------------------------------------------------
    | Example: 1
    */
    'rules_url' => env('APP_URL').'/pages/1',

    /*
    |--------------------------------------------------------------------------
    | FAQ Page URL
    |--------------------------------------------------------------------------
    | Example: 2
    */
    'faq_url' => env('APP_URL').'/pages/2',

    /*
    |--------------------------------------------------------------------------
    | Upload Guide Page URL For Upload Page
    |--------------------------------------------------------------------------
    | Example: 4
    */
    'upload-guide_url' => env('APP_URL').'/pages/4',

    /*
    |--------------------------------------------------------------------------
    | Hide Staff Area Forum Posts From Chat
    |--------------------------------------------------------------------------
    | 1 = Enabled
    | 0 = Disabled
    | If enabled, Staff members get notifications instead of posting being announced in chat.
    */
    'staff-forum-notify' => '0',

    /*
    |--------------------------------------------------------------------------
    | Staff Forum Id
    |--------------------------------------------------------------------------
    | Example: 2
    | The ID value of staff forum area. Should be the main / parent ID.
    */
    'staff-forum-id' => '',

    /*
    |--------------------------------------------------------------------------
    | Thanks system
    |--------------------------------------------------------------------------
    | Allows users to press a button to thank the uploader of a torrent.
    */
    'thanks-system' => [
        /*
        |--------------------------------------------------------------------------
        | Enable the thanks system
        |--------------------------------------------------------------------------
        | True = Enabled
        | False = Disabled
        */
        'is-enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    | Configures outbound email rate limiting.
    */
    'mail' => [
        /*
        |--------------------------------------------------------------------------
        | The number of outbound emails allowed every `mail.every` seconds.
        |--------------------------------------------------------------------------
        | Example: 1
        */
        'allow' => 1,
        /*
        |--------------------------------------------------------------------------
        | The number of seconds between every batch of `mail.allow` outbound emails.
        |--------------------------------------------------------------------------
        | Example: 5
        */
        'every' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | BON
    |--------------------------------------------------------------------------
    | Configures user bon transactions.
    */
    'bon' => [
        /*
        |--------------------------------------------------------------------------
        | The maximum allowed account buffer when buying upload.
        |--------------------------------------------------------------------------
        | null = Disabled
        | Example : 10 * 1024 * 1024 * 1024 * 1024
        */
        'max-buffer-to-buy-upload' => null,
    ],
];
