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
    | TheMovieDB (Movies/TV)
    |--------------------------------------------------------------------------
    |
    | TMDB API Key
    |
    */

    'tmdb' => env('TMDB_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Books (Book metadata lookup)
    |--------------------------------------------------------------------------
    |
    | Optional. The Google Books API works unauthenticated but is subject to
    | a low shared per-IP daily quota. Supplying a key raises that quota.
    |
    */

    'google-books' => env('GOOGLE_BOOKS_API_KEY', ''),
];
