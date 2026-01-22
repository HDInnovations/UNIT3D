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

namespace App\Http\Requests\User;

use App\Models\Torrent;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreTorrentTipRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<Closure|string>|string>
     */
    public function rules(Request $request): array
    {
        $user = $request->user();

        return [
            'torrent_id' => [
                'required',
                'exists:torrents,id',
            ],
            'bon' => [
                'required',
                'numeric',
                'min:1',
                'max:'.$user->seedbonus,
            ],
            'recipient_id' => [
                'required',
                'exists:users,id',
                'different:sender_id',
            ],
            'sender_id' => [
                'required',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bon.min'                => trans('bon.failed-negative'),
            'bon.max'                => trans('bon.failed-funds-poster'),
            'recipient_id.different' => trans('bon.failed-yourself'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender_id'    => auth()->id(),
            'recipient_id' => Torrent::query()->whereKey($this->torrent_id)->value('user_id'),
        ]);
    }
}
