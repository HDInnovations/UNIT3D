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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @see \Tests\Todo\Unit\Http\Requests\StorePollTest
 */
class StoreGiftRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<\Illuminate\Validation\Rules\Exists|string>>
     */
    public function rules(Request $request): array
    {
        $user = $request->user();

        return [
            'recipient_username' => [
                'required',
                Rule::exists('users', 'username')->whereNot('username', $user->username),
            ],
            'bon' => [
                'required',
                'numeric',
                'min:1',
                'max:'.$user->seedbonus,
            ],
            'message' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
