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

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Warning.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $warned_by
 * @property int|null                        $torrent
 * @property string                          $reason
 * @property \Illuminate\Support\Carbon|null $expires_on
 * @property bool                            $active
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Warning extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\WarningFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{expires_on: 'datetime', active: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'expires_on' => 'datetime',
            'active'     => 'bool',
        ];
    }

    /**
     * Get the torrent associated with the warning.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrenttitle(): BelongsTo
    {
        return $this->belongsTo(Torrent::class, 'torrent');
    }

    /**
     * Get the warned user.
     *
     * @return BelongsTo<User, $this>
     */
    public function warneduser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the staff user that issued the warning.
     *
     * @return BelongsTo<User, $this>
     */
    public function staffuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'warned_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the staff user that revoked the warning.
     *
     * @return BelongsTo<User, $this>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Scope query to only include active warnings.
     *
     * @param Builder<Warning> $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', '=', 1);
    }
}
