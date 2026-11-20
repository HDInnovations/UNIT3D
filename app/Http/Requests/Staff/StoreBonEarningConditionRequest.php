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

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Data;

class StoreBonEarningConditionRequest extends Data
{
    public function __construct(
        #[In([
            '1',
            'age',
            'size',
            'seeders',
            'leechers',
            'times_completed',
            'internal',
            'personal_release',
            'seedtime',
            'type_id',
            'connectable',
        ])]
        public string $operand1,
        #[In('<', '>', '<=', '>=', '=', '!=')]
        public string $operator,
        #[Numeric]
        public string $operand2,
    ) {
    }
}
