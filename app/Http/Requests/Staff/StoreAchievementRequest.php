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
use Closure;

class StoreAchievementRequest extends FormRequest
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
            'achievement.position'             => ['required', 'integer'],
            'achievement.is_hidden'            => ['nullable', 'boolean'],
            'achievement.enabled'              => ['nullable', 'boolean'],
            'achievement.filter_type_id'       => ['nullable', 'exists:types,id'],
            'achievement.filter_category_id'   => ['nullable', 'exists:categories,id'],
            'achievement.filter_resolution_id' => ['nullable', 'exists:resolutions,id'],
            'achievement.filter_playlist_id'   => ['nullable', 'exists:playlists,id'],
            'tiers'                            => ['required', 'array', 'min:1'],
            'tiers.*.name'                     => ['required', 'string', 'max:255'],
            'tiers.*.description'              => ['required', 'string', 'max:1000'],
            'tiers.*.threshold'                => ['required', 'numeric', 'min:0'],
            'tiers.*.icon'                     => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Tier thresholds must increase with the tier number: evaluation climbs the tiers in
     * order and stops at the first unmet threshold.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator): void {
                $previous = null;

                foreach ((array) $this->input('tiers', []) as $index => $tier) {
                    $threshold = $tier['threshold'] ?? null;

                    if (!is_numeric($threshold)) {
                        continue;
                    }

                    if ($previous !== null && (float) $threshold <= $previous) {
                        $validator->errors()->add(
                            "tiers.{$index}.threshold",
                            'Each tier threshold must be greater than the previous tier.'
                        );
                    }

                    $previous = (float) $threshold;
                }
            },
        ];
    }
}
