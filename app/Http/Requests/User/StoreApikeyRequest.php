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

namespace App\Http\Requests\User;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreApikeyRequest extends Data
{
    public function __construct(
        public string $name,
        public bool $canSearch,
        public bool $canUpload,
        public bool $canDownload,
        public bool $canViewUser,
        #[Date]
        public Carbon $expiresAt,
    ) {
    }

    /**
     * @return array<string, list<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'expires_at' => [
                new BeforeOrEqual(today()->addYear()),
            ]
        ];
    }
}
