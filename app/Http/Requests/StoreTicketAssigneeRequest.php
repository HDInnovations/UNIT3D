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

use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class StoreTicketAssigneeRequest extends Data
{
    public function __construct(
        #[Exists(User::class, 'id')]
        public int $staffId,
    ) {
    }
}
