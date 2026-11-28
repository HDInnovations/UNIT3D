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

use App\Models\MediaLanguage;
use App\Models\Torrent;
use Closure;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class StoreSubtitleRequest extends Data
{
    public function __construct(
        #[File, Hidden]
        public UploadedFile $subtitleFile,
        #[Exists(MediaLanguage::class, 'id')]
        public int $languageId,
        #[Max(255)]
        public string $note,
        public bool $anon,
        #[Exists(Torrent::class, 'id')]
        public int $torrentId,
    ) {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\Exists|string>>
     */
    public static function rules(): array
    {
        return [
            'subtitle_file' => [
                'required',
                'file',
                function (string $_attribute, mixed $value, Closure $fail): void {
                    if (!\in_array('.'.$value->getClientOriginalExtension(), ['.srt','.ass', '.sup', '.zip'])) {
                        $fail('The Subtitle uploaded does not have a ".srt, .ass, .sup or .zip" file extension (it has "'.$value->getClientOriginalExtension().'"). Did you upload the correct file?');
                    }
                },
            ],
        ];
    }
}
