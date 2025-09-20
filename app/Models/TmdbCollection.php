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
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\TmdbCollection.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $name_sort
 * @property string|null $parts
 * @property string|null $overview
 * @property string|null $poster
 * @property string|null $backdrop
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class TmdbCollection extends Model
{
    /** @use HasFactory<\Database\Factories\TmdbCollectionFactory> */
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the comments for the collection.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the movies that belong to the collection.
     *
     * @return BelongsToMany<TmdbMovie, $this>
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class);
    }
}
