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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TmdbMovie, $this>
     */
    public function movies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TmdbMovie::class);
    }
}
