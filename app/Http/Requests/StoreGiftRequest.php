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
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\Constraints\WhereNotConstraint;
use Spatie\LaravelData\Support\Validation\ValidationRule;

/**
 * @see \Tests\Todo\Unit\Http\Requests\StorePollTest
 */
#[MergeValidationRules]
class StoreGiftRequest extends Data
{
    public function __construct(
        public string $recipientUsername,
        #[Min(1)]
        public int $bon,
        #[Max(255)]
        public string $message,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationRule>>
     */
    public static function rules(): array
    {
        return [
            'recipient_username' => [
                new Rule([
                    new Exists(User::class, 'username', where: new WhereNotConstraint('username', auth()->user()->username)),
                ]),
            ],
            'bon' => [
                new Rule([
                    new Max((float) auth()->user()->seedbonus),
                ]),
            ],
        ];
    }
}
