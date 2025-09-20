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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Occupation.
 *
 * @property int    $id
 * @property int    $position
 * @property string $name
 */
class Occupation extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\OccupationFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the the people that belong to the occupation.
     *
     * @return BelongsToMany<TmdbPerson, $this>
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(TmdbPerson::class, 'tmdb_credits');
    }

    /**
     * Get all the credits for this occupation.
     *
     * @return HasMany<TmdbCredit, $this>
     */
    public function credits(): HasMany
    {
        return $this->hasMany(TmdbCredit::class);
    }
}
