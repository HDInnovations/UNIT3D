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

namespace App\Data\Staff;

use Illuminate\Http\UploadedFile;
use App\Attributes\FromCurrentUserId;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class ArticleData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        #[Max(65535)]
        public string $content,
        #[Max(10240)]
        public ?UploadedFile $image,
        #[FromCurrentUserId]
        public int $userId,
    ) {
    }
}
