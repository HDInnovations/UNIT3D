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
use App\Models\DonationPackage;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class StoreDonationRequest extends Data
{
    public function __construct(
        #[Exists(DonationPackage::class, 'id')]
        public int $packageId,
        #[Min(1), Max(255)]
        public string $transaction,
        #[FromCurrentUserId]
        public int $userId,
    ) {
    }
}
