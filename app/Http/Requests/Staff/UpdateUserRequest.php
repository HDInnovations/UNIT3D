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

use App\Models\Group;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class UpdateUserRequest extends Data
{
    public function __construct(
        public string $username,
        #[Min(0)]
        public int $uploaded,
        #[Min(0)]
        public int $downloaded,
        #[Max(255)]
        public ?string $title,
        #[Max(16 * 1024 * 1024)]
        public string $about,
        #[Exists(Group::class, 'id')]
        public int $groupId,
        public string $seedbonus,
        #[Min(0)]
        public int $invites,
        #[Min(0)]
        public int $flTokens,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array<\Illuminate\Contracts\Validation\Rule|string>|string>
     */
    public static function rules(): array
    {
        return [
            'seedbonus' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],
        ];
    }
}
