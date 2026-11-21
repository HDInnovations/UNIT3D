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

use App\Attributes\FromCurrentUserId;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\ValidationAttribute;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreTicketRequest extends Data
{
    public function __construct(
        #[Exists(TicketCategory::class, 'id')]
        public int $categoryId,
        #[Exists(TicketPriority::class, 'id')]
        public int $priorityId,
        #[Max(255)]
        public string $subject,
        #[Max(65535)]
        public string $body,
        #[FromCurrentUserId]
        public int $userId,
        /** @var Collection<int, UploadedFile> */
        public Collection $attachments = new Collection(),
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<ValidationAttribute>>
     */
    public static function rules(): array
    {
        return [
            'options.*' => [
                new File(),
                new Image(),
                new Max(1024),
            ],
        ];
    }
}
