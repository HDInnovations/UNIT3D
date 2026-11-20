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

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule as LaravelRule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\ListType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationRule;

#[MergeValidationRules]
class StoreApplicationRequest extends Data
{
    public function __construct(
        public StoreApplicationApplicationRequest $application,
        /** @var Collection<int, StoreApplicationImageRequest> */
        #[ListType, Min(2), Max(30)]
        public Collection $images,
        /** @var Collection<int, StoreApplicationLinkRequest> */
        public Collection $links,
        public ?string $captcha,
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
            'captcha' => [
                new Rule([
                    LaravelRule::when(config('captcha.enabled'), 'hiddencaptcha'),
                ])
            ]
        ];
    }
}
