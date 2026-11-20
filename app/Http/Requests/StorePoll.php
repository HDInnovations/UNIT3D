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

namespace App\Http\Requests;

use App\Attributes\FromCurrentUserId;
use App\Http\Requests\Staff\StorePollOptionRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\ListType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

/**
 * @see \Tests\Todo\Unit\Http\Requests\StorePollTest
 */
class StorePoll extends Data
{
    public function __construct(
        #[Min(10)]
        public string $title,
        #[Date]
        public ?Carbon $expiresAt,
        public bool $multipleChoice,
        #[FromCurrentUserId]
        public int $userId,
        /** @var Collection<int, StorePollOptionRequest> */
        #[Min(2), Max(20), ListType]
        public Collection $options,
    ) {
    }
}
