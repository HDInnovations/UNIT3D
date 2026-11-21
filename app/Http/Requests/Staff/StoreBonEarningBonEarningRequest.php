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

namespace App\Http\Requests\Staff;

use App\Models\BonEarning;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class StoreBonEarningBonEarningRequest extends Data
{
    public function __construct(
        #[Unique(BonEarning::class, 'position')]
        public int $position,
        #[Max(255)]
        public string $name,
        #[Max(255)]
        public string $description,
        #[In([
            '1',
            'age',
            'size',
            'seeders',
            'leechers',
            'times_completed',
            'seedtime',
            'personal_release',
            'internal',
            'connectable'
        ])]
        public string $variable,
        #[Numeric]
        public string $multiplier,
        #[In('append', 'multiply')]
        public string $operation,
    ) {
    }
}
