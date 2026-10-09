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

namespace App\Helpers;

class StringHelper
{
    private const array units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

    private const array secondsPer = [
        'year'   => 3600 * 24 * 365,
        'month'  => 3600 * 24 * 30,
        'week'   => 3600 * 24 * 7,
        'day'    => 3600 * 24,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    ];

    public static function formatBytes(int|float $bytes = 0, int $precision = 2): string
    {
        if ($bytes === INF) {
            return '∞';
        }

        $minus = false;

        if ($bytes < 0) {
            $minus = true;
            $bytes *= -1;
        }

        for ($i = 0; ($bytes / 1024) > 0.9 && ($i < \count(self::units) - 1); $i++) {
            $bytes /= 1024;
        }

        $result = round($bytes, $precision);

        if ($minus) {
            $result *= -1;
        }

        return $result."\u{a0}".self::units[$i];
    }

    public static function timeElapsed(int|float $seconds): string
    {
        $seconds = \intval($seconds);

        if ($seconds == 0) {
            return 'N/A';
        }

        $units = [];

        foreach (self::secondsPer as $unit => $secondsPer) {
            $magnitude = intdiv($seconds, $secondsPer);

            if ($magnitude > 0) {
                $units[] = $magnitude.trans('common.abbrev-'.$unit.'s');
                $seconds -= $magnitude * $secondsPer;
            }
        }

        return implode($units);
    }
}
