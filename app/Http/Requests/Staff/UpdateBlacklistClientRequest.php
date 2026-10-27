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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateBlacklistClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\Rules\Unique|string>>
     */
    public function rules(Request $request): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blacklist_clients')->ignore($request->route('blacklistClient')),
            ],
            'peer_id_prefix' => [
                Rule::unique('blacklist_clients')->ignore($request->route('blacklistClient')),
            ],
            'reason' => [
                'required',
                'string',
                'max:65536',
            ],
        ];
    }
}
