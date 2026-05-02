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
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Requests\Staff;

use App\Enums\AchievementConditionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAchievementRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'achievement.name'                 => ['required', 'string', 'max:255'],
            'achievement.category'             => ['required', 'string', 'max:255'],
            'achievement.type'                 => ['required', Rule::enum(AchievementConditionType::class)],
            'achievement.icon'                 => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'achievement.positions'            => ['required', 'integer'],
            'achievement.is_hidden'            => ['nullable', 'boolean'],
            'achievement.enabled'              => ['nullable', 'boolean'],
            'achievement.filter_type_id'       => ['nullable', 'exists:types,id'],
            'achievement.filter_category_id'   => ['nullable', 'exists:categories,id'],
            'achievement.filter_resolution_id' => ['nullable', 'exists:resolutions,id'],
            'achievement.filter_playlist_id'   => ['nullable', 'exists:playlists,id'],
            'tiers'                            => ['required', 'array', 'min:1'],
            'tiers.*.id'                       => ['nullable', 'integer'],
            'tiers.*.name'                     => ['required', 'string', 'max:255'],
            'tiers.*.description'              => ['required', 'string', 'max:1000'],
            'tiers.*.threshold'                => ['required', 'numeric', 'min:0'],
            'tiers.*.icon'                     => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }
}
