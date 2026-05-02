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

namespace App\Models;

use App\Enums\AchievementConditionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AllowDynamicProperties;

/**
 * App\Models\Achievement.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $category
 * @property AchievementConditionType        $type
 * @property string|null                     $icon_path
 * @property int                             $positions
 * @property bool                            $is_hidden
 * @property bool                            $enabled
 * @property int|null                        $filter_type_id
 * @property int|null                        $filter_category_id
 * @property int|null                        $filter_resolution_id
 * @property int|null                        $filter_playlist_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[AllowDynamicProperties]
final class Achievement extends Model
{
    /** @use HasFactory<\Database\Factories\AchievementFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<string, string>, never>
     */
    protected function casts(): array
    {
        return [
            'type'      => AchievementConditionType::class,
            'is_hidden' => 'bool',
            'enabled'   => 'bool',
        ];
    }

    /**
     * @return HasMany<AchievementTier, $this>
     */
    public function tiers(): HasMany
    {
        return $this->hasMany(AchievementTier::class)->orderBy('tier');
    }

    /**
     * @return HasMany<UserAchievement, $this>
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * @return BelongsTo<Type, $this>
     */
    public function filterType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'filter_type_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function filterCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'filter_category_id');
    }

    /**
     * @return BelongsTo<Resolution, $this>
     */
    public function filterResolution(): BelongsTo
    {
        return $this->belongsTo(Resolution::class, 'filter_resolution_id');
    }

    /**
     * @return BelongsTo<Playlist, $this>
     */
    public function filterPlaylist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class, 'filter_playlist_id');
    }
}
