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
 * @author     Obi-Wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CollectibleItem.
 *
 * @property int                             $id
 * @property int                             $collection_id
 * @property string                          $name
 * @property string                          $description
 * @property bool                            $resell
 * @property float                           $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Collectible extends Model
{
    /** @use HasFactory<\Database\Factories\CollectibleFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     resell: 'bool',
     * }
     */
    protected function casts(): array
    {
        return [
            'resell' => 'bool',
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Belongs To A Collection Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<CollectibleCategory, $this>
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CollectibleCategory::class);
    }

    /**
     * An Item Has Requirements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<CollectibleRequirement, $this>
     */
    public function requirements(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->HasOne(CollectibleRequirement::class, 'collectible_id');
    }

    /**
     * An Item Has Many Items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CollectibleItem, $this>
     */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CollectibleItem::class, 'collectible_id');
    }

    /**
     * An Item Has Many Items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CollectibleTransaction, $this>
     */
    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CollectibleTransaction::class, 'collectible_id');
    }

    /**
     * An Item Has Many Offers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CollectibleOffer, $this>
     */
    public function offers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CollectibleOffer::class, 'collectible_id');
    }

    /**
     * An Item Is In Stock.
     *
     * @return bool
     */
    public function getInStockAttribute()
    {
        return $this->items()->whereNull('user_id')->exists();
    }
}
