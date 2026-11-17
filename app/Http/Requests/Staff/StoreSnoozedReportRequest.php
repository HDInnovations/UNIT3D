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

use Illuminate\Support\Carbon;
use Override;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;

class StoreSnoozedReportRequest extends Data
{
    public function __construct(
        #[Date]
        public Carbon $snoozedUntil,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        if (ctype_digit($properties['snoozed_days'] ?? '')) {
            $properties['snoozed_until'] = now()->addDays((int) $properties['snoozed_until'])->toDateTimeString();
        }

        return $properties;
    }
}
