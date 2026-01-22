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

use Illuminate\Support\Facades\DB;
use Exception;

class SystemInformation
{
    /**
     * @var string[]
     */
    private const UNITS = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];

    /**
     * @var string[]
     */
    private const KNOWN_DATABASES = [
        'sqlite',
        'mysql',
        'pgsql',
        'sqlsrv',
    ];

    /**
     * @return array{
     *     '1-minute': string,
     *     '5-minute': string,
     *     '15-minute': string,
     * }
     */
    public function avg(): ?array
    {
        $loads = sys_getloadavg();

        if ($loads === false) {
            return [
                '1-minute'  => __('common.unknown'),
                '5-minute'  => __('common.unknown'),
                '15-minute' => __('common.unknown'),
            ];
        }

        return [
            '1-minute'  => number_format($loads[0], 2),
            '5-minute'  => number_format($loads[1], 2),
            '15-minute' => number_format($loads[2], 2),
        ];
    }

    /**
     * @return array{
     *     total: string,
     *     available: string,
     *     used: string,
     * }
     */
    public function memory(): ?array
    {
        if (!is_readable('/proc/meminfo')) {
            return [
                'total'     => __('common.unknown'),
                'available' => __('common.unknown'),
                'used'      => __('common.unknown'),
            ];
        }

        $content = file_get_contents('/proc/meminfo');

        if ($content === false) {
            return [
                'total'     => __('common.unknown'),
                'available' => __('common.unknown'),
                'used'      => __('common.unknown'),
            ];
        }

        preg_match('#^MemTotal: \s*(\d*)#m', $content, $matches);
        $total = ((int) $matches[1]) * 1_024;
        preg_match('/^MemAvailable: \s*(\d*)/m', $content, $matches);
        $available = ((int) $matches[1]) * 1_024;

        return [
            'total'     => $this->formatBytes($total),
            'available' => $this->formatBytes($available),
            'used'      => $this->formatBytes($total - $available),
        ];
    }

    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $bytes = max($bytes, 0);
        $pow = (int) floor(($bytes ? log($bytes) : 0) / log(1_024));
        $pow = min($pow, (\count(self::UNITS)) - 1);
        // Uncomment one of the following alternatives
        $bytes /= 1_024 ** $pow;
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.self::UNITS[$pow];
    }

    /**
     * @return array{
     *     total: string,
     *     free: string,
     *     used: string,
     * }
     */
    public function disk(): array
    {
        $total = disk_total_space(base_path());
        $free = disk_free_space(base_path());

        if ($total === false || $free === false) {
            return [
                'total' => __('common.unknown'),
                'free'  => __('common.unknown'),
                'used'  => __('common.unknown'),
            ];
        }

        return [
            'total' => $this->formatBytes($total),
            'free'  => $this->formatBytes($free),
            'used'  => $this->formatBytes($total - $free),
        ];
    }

    public function uptime(): ?float
    {
        if (!is_readable('/proc/uptime')) {
            return null;
        }

        $uptime = file_get_contents('/proc/uptime');

        if ($uptime === false) {
            return null;
        }

        return (float) $uptime;
    }

    /**
     * @return array{
     *     os: string,
     *     php: string,
     *     database: string,
     *     laravel: string,
     * }
     */
    public function basic(): array
    {
        return [
            'os'       => PHP_OS,
            'php'      => PHP_VERSION,
            'database' => $this->getDatabase(),
            'laravel'  => app()->version(),
        ];
    }

    private function getDatabase(): string
    {
        if (!\in_array(config('database.default'), self::KNOWN_DATABASES, true)) {
            return 'Unknown';
        }

        return DB::select('select version()')[0]->{'version()'};
    }

    /**
     * Get all the directory permissions as well as the recommended ones.
     *
     * @return array<
     *     int,
     *     array{
     *         directory: string,
     *         permission: string,
     *         recommended: string,
     *     }
     * >
     */
    public function directoryPermissions(): array
    {
        return [
            [
                'directory'   => base_path('bootstrap/cache'),
                'permission'  => $this->getDirectoryPermission('bootstrap/cache'),
                'recommended' => '0775',
            ],
            [
                'directory'   => base_path('public'),
                'permission'  => $this->getDirectoryPermission('public'),
                'recommended' => '0775',
            ],
            [
                'directory'   => base_path('storage'),
                'permission'  => $this->getDirectoryPermission('storage'),
                'recommended' => '0775',
            ],
            [
                'directory'   => base_path('vendor'),
                'permission'  => $this->getDirectoryPermission('vendor'),
                'recommended' => '0775',
            ],
        ];
    }

    /**
     * Get the file permissions for a specific path/file.
     */
    private function getDirectoryPermission(string $path): string
    {
        try {
            return substr(\sprintf('%o', fileperms(base_path($path))), -4);
        } catch (Exception) {
            return __('common.unknown');
        }
    }
}
