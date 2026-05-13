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

namespace App\Support;

use Illuminate\Support\Arr;

final class AutogroupRequirementUnits
{
    /**
     * @var array<string, int>
     */
    public const array BYTE_UNITS = [
        'bytes' => 1,
        'mb'    => 1024 ** 2,
        'gb'    => 1024 ** 3,
        'tb'    => 1024 ** 4,
    ];

    /**
     * @var array<string, int>
     */
    public const array TIME_UNITS = [
        'seconds' => 1,
        'days'    => 86400,
        'weeks'   => 604800,
        'months'  => 2592000,
        'years'   => 31536000,
    ];

    /**
     * @var list<string>
     */
    public const array UNIT_FIELDS = [
        'min_uploaded_unit',
        'min_seedsize_unit',
        'min_age_unit',
        'min_avg_seedtime_unit',
    ];

    /**
     * @param  array<string, mixed> $group
     * @return array<string, mixed>
     */
    public static function convertGroup(array $group): array
    {
        $group['min_uploaded'] = self::convert($group['min_uploaded'] ?? null, $group['min_uploaded_unit'] ?? 'bytes', self::BYTE_UNITS);
        $group['min_seedsize'] = self::convert($group['min_seedsize'] ?? null, $group['min_seedsize_unit'] ?? 'bytes', self::BYTE_UNITS);
        $group['min_age'] = self::convert($group['min_age'] ?? null, $group['min_age_unit'] ?? 'seconds', self::TIME_UNITS);
        $group['min_avg_seedtime'] = self::convert($group['min_avg_seedtime'] ?? null, $group['min_avg_seedtime_unit'] ?? 'seconds', self::TIME_UNITS);

        return $group;
    }

    /**
     * @param  array<string, mixed> $group
     * @return array<string, mixed>
     */
    public static function stripUnitFields(array $group): array
    {
        return Arr::except($group, self::UNIT_FIELDS);
    }

    /**
     * @return array{value: int|null, unit: string}
     */
    public static function displayByte(?int $value): array
    {
        return self::display($value, array_reverse(self::BYTE_UNITS, true), 'bytes');
    }

    /**
     * @return array{value: int|null, unit: string}
     */
    public static function displayTime(?int $value): array
    {
        return self::display($value, array_reverse(self::TIME_UNITS, true), 'seconds');
    }

    /**
     * @param array<string, int> $units
     */
    private static function convert(mixed $value, mixed $unit, array $units): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false || ! \is_string($unit) || ! isset($units[$unit])) {
            return $value;
        }

        return (int) $value * $units[$unit];
    }

    /**
     * @param  array<string, int>                   $units
     * @return array{value: int|null, unit: string}
     */
    private static function display(?int $value, array $units, string $defaultUnit): array
    {
        if ($value === null) {
            return [
                'value' => null,
                'unit'  => $defaultUnit,
            ];
        }

        foreach ($units as $unit => $multiplier) {
            if ($multiplier !== 1 && $value >= $multiplier && $value % $multiplier === 0) {
                return [
                    'value' => (int) ($value / $multiplier),
                    'unit'  => $unit,
                ];
            }
        }

        return [
            'value' => $value,
            'unit'  => $defaultUnit,
        ];
    }
}
