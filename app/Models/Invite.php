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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use AllowDynamicProperties;
use App\Models\Scopes\ApprovedScope;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Invite.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $email
 * @property string                          $code
 * @property string|null                     $expires_on
 * @property int|null                        $accepted_by
 * @property string|null                     $accepted_at
 * @property string|null                     $custom
 * @property string|null                     $internal_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
#[AllowDynamicProperties]
final class Invite extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\InviteFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * The user that sent the invite.
     *
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * The user that received the invite.
     *
     * @return BelongsTo<User, $this>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by')->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * The application that triggered the invite.
     *
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'email', 'email')
            ->whereBetween('applications.moderated_at', [
                DB::raw('invites.created_at - INTERVAL 5 SECOND'),
                DB::raw('invites.created_at + INTERVAL 5 SECOND'),
            ])
            ->withoutGlobalScope(ApprovedScope::class);
    }
}
