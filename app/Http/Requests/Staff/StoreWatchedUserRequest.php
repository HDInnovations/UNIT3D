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

use App\Attributes\FromCurrentUserId;
use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class StoreWatchedUserRequest extends Data
{
    public function __construct(
        #[Min(3)]
        public string $message,
        #[Exists(User::class, 'id')]
        public int $userId,
        #[FromCurrentUserId]
        public int $staffId,
    ) {
    }
}
