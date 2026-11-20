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

namespace App\Http\Requests;

use App\Http\Requests\Staff\UpdatePollOptionRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class UpdatePollRequest extends Data
{
    public function __construct(
        #[Min(10)]
        public string $title,
        #[Date]
        public ?Carbon $expiresAt,
        public bool $multipleChoice,
        /** @var Collection<int, UpdatePollOptionRequest> */
        #[Min(2), Max(20)]
        public Collection $options,
    ) {
    }
}
