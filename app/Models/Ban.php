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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Ban.
 *
 * @property int                             $id
 * @property int                             $owned_by
 * @property int|null                        $created_by
 * @property string|null                     $ban_reason
 * @property string|null                     $unban_reason
 * @property string|null                     $removed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Ban extends Model
{
    /** @use HasFactory<\Database\Factories\BanFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the banned user.
     *
     * @return BelongsTo<User, $this>
     */
    public function banneduser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owned_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the staff user who issued the ban.
     *
     * @return BelongsTo<User, $this>
     */
    public function staffuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }
}
