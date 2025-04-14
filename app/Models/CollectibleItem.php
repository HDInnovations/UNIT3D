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
 * App\Models\CollectibleOffer.
 *
 * @property int                             $id
 * @property int                             $collectible_id
 * @property int                             $user_id
 * @property bool                            $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class CollectibleItem extends Model
{
    /** @use HasFactory<\Database\Factories\CollectibleItemFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * A Item Belongs To A Collectible.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Collectible, $this>
     */
    public function collectible(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Collectible::class);
    }

    /**
     * A Item Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A Item Belongs To A Collectible Transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<CollectibleTransaction, $this>
     */
    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CollectibleTransaction::class, 'collectible_id');
    }

    /**
     * A Item Belongs To A Collectible Transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<CollectibleTransaction, $this>
     */
    public function userTransaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->transaction()->where('buyer_id', $this->user->id)->latest();
    }
}
