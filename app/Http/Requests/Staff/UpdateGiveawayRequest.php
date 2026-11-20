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
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class UpdateGiveawayRequest extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Max(65535)]
        public string $description,
        #[Max(255)]
        public string $icon,
        #[Date]
        public Carbon $startsAt,
        #[Date]
        public Carbon $endsAt,
        public bool $active,
    ) {
    }
}
