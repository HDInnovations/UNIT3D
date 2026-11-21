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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AllowDynamicProperties;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * App\Models\Apikey.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $name
 * @property string                          $content
 * @property bool                            $can_search
 * @property bool                            $can_upload
 * @property bool                            $can_download
 * @property bool                            $can_view_user
 * @property \Illuminate\Support\Carbon      $expires_at
 * @property \Illuminate\Support\Carbon|null $reminded_expiry_at
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property string|null                     $created_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
#[AllowDynamicProperties]
final class Apikey extends Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{deleted_at: 'datetime'}
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'created_at'         => 'datetime',
            'deleted_at'         => 'datetime',
            'expires_at'         => 'datetime',
            'reminded_expiry_at' => 'datetime',
            'last_used_at'       => 'datetime',
        ];
    }

    /**
     * Get the user that owns the apikey.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
