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
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\ValidationAttribute;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

#[MergeValidationRules]
class UpdateRssRequest extends Data
{
    public function __construct(
        #[Between(3, 255)]
        public string $name,
        #[Max(999)]
        public int $position,
        #[Max(255)]
        public Optional|string $search,
        #[Max(255)]
        public Optional|string $uploader,
        /** @var list<int>|Optional */
        #[Max(999)]
        public Optional|array $categories,
        /** @var list<int>|Optional */
        #[Max(999)]
        public Optional|array $types,
        /** @var list<int>|Optional */
        #[Max(999)]
        public Optional|array $resolutions,
        /** @var list<int>|Optional */
        public Optional|array $genres,
        public Optional|int $imdb,
        public Optional|int $tvdb,
        public Optional|int $tmdb,
        public Optional|int $mal,
        public Optional|bool $freeleech,
        public Optional|bool $doubleupload,
        public Optional|bool $featured,
        public Optional|bool $highspeed,
        public Optional|bool $internal,
        public Optional|bool $personalrelease,
        public Optional|bool $bookmark,
        public Optional|bool $alive,
        public Optional|bool $dying,
        public Optional|bool $dead,
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
