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

namespace App\Http\Requests\Staff;

use App\Models\Category;
use App\Models\Resolution;
use App\Models\Type;
use Closure;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreAutomaticTorrentFreeleechRequest extends Data
{
    public function __construct(
        public ?string $nameRegex,
        #[Min(0)]
        public int $position,
        #[Min(0)]
        public ?int $size,
        #[Exists(Category::class, 'id')]
        public ?int $categoryId,
        #[Exists(Type::class, 'id')]
        public ?int $typeId,
        #[Exists(Resolution::class, 'id')]
        public ?int $resolutionId,
        #[Min(0), Max(100)]
        public int $freeleechPercentage,
    ) {
    }

    /**
     * @return array<string, array<int, (Closure)|string>>
     */
    public static function rules(): array
    {
        return [
            'name_regex' => [
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if (@preg_match($value, 'Validate regex') === false) {
                        $fail('Regex syntax error.');
                    }
                },
            ],
        ];
    }
}
