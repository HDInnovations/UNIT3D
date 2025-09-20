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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\BonEarning.
 *
 * @property int                                                                                                            $id
 * @property int                                                                                                            $position
 * @property '1'|'age'|'size'|'seeders'|'leechers'|'times_completed'|'seedtime'|'personal_release'|'internal'|'connectable' $variable
 * @property float                                                                                                          $multiplier
 * @property 'append'|'multiply'                                                                                            $operation
 * @property string                                                                                                         $name
 * @property string                                                                                                         $description
 */
class BonEarning extends Model
{
    /**
     * Indicates if the model should be timestamped.
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
     * Get the conditions for the bon earning.
     *
     * @return HasMany<BonEarningCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(BonEarningCondition::class);
    }
}
