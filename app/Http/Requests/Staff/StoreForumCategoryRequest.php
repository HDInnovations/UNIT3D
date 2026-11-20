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

use Illuminate\Support\Str;
use Override;
use Spatie\LaravelData\Data;

class StoreForumCategoryRequest extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $position,
        public string $description,
    ) {
    }

    /**
     * @param array<mixed> $properties
     *
     * @return array<mixed>
     */
    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        $properties['slug'] = Str::slug($properties['name'] ?? '');

        return $properties;
    }
}
