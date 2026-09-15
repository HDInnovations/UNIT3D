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

enum Occupation: int
{
    case CREATOR = 1;
    case DIRECTOR = 2;
    case WRITER = 3;
    case PRODUCER = 4;
    case COMPOSER = 5;
    case CINEMATOGRAPHER = 6;
    case EDITOR = 7;
    case PRODUCTION_DESIGNER = 8;
    case ART_DIRECTOR = 9;
    case ACTOR = 10;

    public static function from_tmdb_job(string $job_name): ?Occupation
    {
        return match ($job_name) {
            "Director"   => self::DIRECTOR,
            "Screenplay" => self::WRITER,
            "Producer", "Co-Producer", "Associate Producer" => self::PRODUCER,
            "Original Music Composer" => self::COMPOSER,
            "Director of Photography" => self::CINEMATOGRAPHER,
            "Editor"                  => self::EDITOR,
            "Production Design"       => self::PRODUCTION_DESIGNER,
            "Art Direction"           => self::ART_DIRECTOR,
            default                   => null,
        };
    }
}
