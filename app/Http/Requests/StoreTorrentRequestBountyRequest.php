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

use App\Attributes\FromCurrentUserId;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreTorrentRequestBountyRequest extends Data
{
    public function __construct(
        public int $seedbonus,
        public bool $anon,
        #[FromCurrentUserId]
        public int $userId,
    ) {
    }

    /**
     * @return array<string, list<ValidationRule>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'seedbonus' => [
                new Between(config('other.bon.min-bounty'), (float) auth()->user()->seedbonus),
            ]
        ];
    }
}
