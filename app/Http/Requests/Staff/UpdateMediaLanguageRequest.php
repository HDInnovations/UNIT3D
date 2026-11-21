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

use App\Models\MediaLanguage;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class UpdateMediaLanguageRequest extends Data
{
    public function __construct(
        #[Unique(MediaLanguage::class, ignore: new RouteParameterReference('mediaLanguage'))]
        public string $name,
        #[Unique(MediaLanguage::class, ignore: new RouteParameterReference('mediaLanguage'))]
        public string $code,
    ) {
    }
}
