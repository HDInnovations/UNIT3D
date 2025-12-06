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
 * @author     Obi-wana
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AllowDynamicProperties;

/**
 * App\Models\UploadContest.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $description
 * @property string                          $icon
 * @property bool                            $active
 * @property bool                            $awarded
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[AllowDynamicProperties]
final class UploadContest extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\UploadContestFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{starts_at: 'datetime', ends_at: 'datetime', active: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'active'    => 'bool',
            'awarded'   => 'bool',
        ];
    }

    /**
     * Get the available prizes for the upload contest.
     *
     * @return HasMany<UploadContestPrize, $this>
     */
    public function prizes(): HasMany
    {
        return $this->hasMany(UploadContestPrize::class);
    }
}
