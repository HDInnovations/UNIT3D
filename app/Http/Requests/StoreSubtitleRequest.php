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

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubtitleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure(string, mixed, Closure(string): never): void|\Illuminate\Validation\Rules\Exists|string>>
     */
    public function rules(): array
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
            'language_id' => [
                'required',
                Rule::exists('media_languages', 'id'),
            ],
            'note' => [
                'required',
                'max:65535',
            ],
            'anon' => [
                'required',
                'boolean',
            ],
            'torrent_id' => [
                'required',
                'integer',
                Rule::exists('torrents', 'id'),
            ],
        ];
    }
}
