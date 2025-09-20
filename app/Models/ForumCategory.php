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

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Forum.
 *
 * @property int                             $id
 * @property int|null                        $position
 * @property string                          $name
 * @property string                          $slug
 * @property string                          $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ForumCategory extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ForumCategoryFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at'];

    /**
     * Has the topics for the category.
     *
     * @return HasManyThrough<Topic, Forum, $this>
     */
    public function topics(): HasManyThrough
    {
        return $this->hasManyThrough(Topic::class, Forum::class);
    }

    /**
     * Get the forums for the category.
     *
     * @return HasMany<Forum, $this>
     */
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class);
    }
}
