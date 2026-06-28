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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\DTO;

readonly class RssTorrentDTO
{
    // Fields sometimes missing from json array in database:
    // - resolutions
    // - bookmark
    // - personalrelease
    public function __construct(
        public ?string $search = null,
        public ?string $uploader = null,
        /** @var null|list<string> // string is the id */
        public ?array $categories = null,
        /** @var null|list<string> // string can be name or id */
        public ?array $types = null,
        /** @var null|list<string> // string can be name or id */
        public ?array $resolutions = null,
        /** @var null|list<string> // string can be name or id */
        public ?array $genres = null,
        public ?string $tmdb = null,
        public ?string $imdb = null,
        public ?string $tvdb = null,
        public ?string $mal = null,
        /** @var null|'1' */
        public ?string $freeleech = null,
        /** @var null|'1' */
        public ?string $doubleupload = null,
        /** @var null|'1' */
        public ?string $featured = null,
        /** @var null|'1' */
        public ?string $highspeed = null,
        /** @var null|'1' */
        public ?string $bookmark = null,
        /** @var null|'1' */
        public ?string $internal = null,
        /** @var null|'1' */
        public ?string $personalrelease = null,
        /** @var null|'1' */
        public ?string $alive = null,
        /** @var null|'1' */
        public ?string $dying = null,
        /** @var null|'0'|'1' */
        public ?string $dead = null,
    ) {
    }
}
