<?php

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

declare(strict_types=1);

namespace App\Enums;

enum GlobalRateLimit: string
{
    case ANNOUNCE = 'announce';
    case API = 'api';
    case AUTHENTICATED_IMAGES = 'authenticated-images';
    case CHAT = 'chat';
    case EMAIL_VERIFICATION = 'email-verification';
    case FORGOT_PASSWORD = 'forgot-password';
    case IGDB = 'igdb';
    case REGISTER = 'register';
    case RESET_PASSWORD = 'reset-password';
    case RSS = 'rss';
    case SEARCH = 'search';
    case TMDB = 'tmdb';
    case WEB = 'web';
}
