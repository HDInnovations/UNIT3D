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

use App\Models\Category;
use App\Models\Resolution;
use App\Models\TmdbGenre;
use App\Models\Type;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\ValidationAttribute;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

#[MergeValidationRules]
class StoreRssRequest extends Data
{
    public function __construct(
        #[Min(3), Max(255)]
        public string $name,
        #[Max(999)]
        public int $position,
        #[Max(255)]
        public ?string $search = null,
        #[Max(255)]
        public ?string $uploader = null,
        /** @var ?list<int> */
        #[Max(999)]
        public ?array $categories = null,
        /** @var ?list<int> */
        #[Max(999)]
        public ?array $types = null,
        /** @var ?list<int> */
        #[Max(999)]
        public ?array $resolutions = null,
        /** @var ?list<int> */
        public ?array $genres = null,
        public ?int $imdb = null,
        public Optional|int $tvdb = new Optional(),
        public Optional|int $tmdb = new Optional(),
        public ?int $mal = null,
        public ?bool $freeleech = null,
        public ?bool $doubleupload = null,
        public ?bool $featured = null,
        public ?bool $highspeed = null,
        public ?bool $internal = null,
        public ?bool $personalrelease = null,
        public ?bool $bookmark = null,
        public ?bool $alive = null,
        public ?bool $dying = null,
        public ?bool $dead = null,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationAttribute|string>>
     */
    public static function rules(): array
    {
        return [
            'categories.*' => [
                'sometimes',
                'integer',
                new Exists(Category::class, 'id'),
            ],
            'types.*' => [
                'sometimes',
                'integer',
                new Exists(Type::class, 'id'),
            ],
            'resolutions.*' => [
                'sometimes',
                'integer',
                new Exists(Resolution::class, 'id'),
            ],
            'genres.*' => [
                'sometimes',
                'integer',
                new Exists(TmdbGenre::class, 'id'),
            ],
        ];
    }
}
