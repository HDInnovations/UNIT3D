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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\TmdbGenre.
 *
 * @property int    $id
 * @property string $name
 */
class TmdbGenre extends Model
{
    /** @use HasFactory<\Database\Factories\TmdbGenreFactory> */
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the movies that belong to the genre.
     *
     * @return BelongsToMany<TmdbMovie, $this>
     */
    public function movie(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class);
    }

    /**
     * Get the tv shows that belong to the genre.
     *
     * @return BelongsToMany<TmdbTv, $this>
     */
    public function tv(): BelongsToMany
    {
        return $this->belongsToMany(TmdbTv::class);
    }
}
