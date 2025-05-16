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
 * App\Models\CollectibleItemRequirement.
 *
 * @property int    $id
 * @property int    $collectible_id
 * @property int    $min_uploaded
 * @property int    $min_seedsize
 * @property int    $min_avg_seedtime
 * @property string $min_ratio
 * @property int    $min_age
 */
class CollectibleRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\CollectibleRequirementFactory> */
    use HasFactory;

    /**
     * Indicates If The Model Should Be Timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * A Requirement Belongs To A Collectible.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Collectible, $this>
     */
    public function collectible(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Collectible::class, 'collectible_id');
    }
}
