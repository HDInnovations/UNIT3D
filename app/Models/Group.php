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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Group.
 *
 * @property int      $id
 * @property string   $name
 * @property string   $slug
 * @property int      $position
 * @property int      $level
 * @property int|null $download_slots
 * @property string   $description
 * @property string   $color
 * @property string   $icon
 * @property string   $effect
 * @property bool     $is_uploader
 * @property bool     $is_internal
 * @property bool     $is_editor
 * @property bool     $is_torrent_modo
 * @property bool     $is_owner
 * @property bool     $is_admin
 * @property bool     $is_modo
 * @property bool     $is_trusted
 * @property bool     $is_immune
 * @property bool     $is_freeleech
 * @property bool     $is_double_upload
 * @property bool     $is_refundable
 * @property bool     $can_chat
 * @property bool     $can_comment
 * @property bool     $can_invite
 * @property bool     $can_request
 * @property bool     $can_upload
 * @property bool     $is_incognito
 * @property bool     $autogroup
 * @property bool     $system_required
 * @property int      $min_uploaded
 * @property int      $min_seedsize
 * @property int      $min_avg_seedtime
 * @property string   $min_ratio
 * @property int      $min_age
 * @property int      $min_uploads
 */
class Group extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\GroupFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array{
     *     is_uploader: 'bool',
     *     is_internal: 'bool',
     *     is_editor: 'bool',
     *     is_torrent_modo: 'bool',
     *     is_owner: 'bool',
     *     is_admin: 'bool',
     *     is_modo: 'bool',
     *     is_trusted: 'bool',
     *     is_immune: 'bool',
     *     is_freeleech: 'bool',
     *     is_double_upload: 'bool',
     *     is_refundable: 'bool',
     *     can_chat: 'bool',
     *     can_comment: 'bool',
     *     can_invite: 'bool',
     *     can_request: 'bool',
     *     can_upload: 'bool',
     *     is_incognito: 'bool',
     *     autogroup: 'bool',
     *     system_required: 'bool',
     *     min_ratio: 'decimal:2',
     * }
     */
    protected function casts(): array
    {
        return [
            'is_uploader'      => 'bool',
            'is_internal'      => 'bool',
            'is_editor'        => 'bool',
            'is_torrent_modo'  => 'bool',
            'is_owner'         => 'bool',
            'is_admin'         => 'bool',
            'is_modo'          => 'bool',
            'is_trusted'       => 'bool',
            'is_immune'        => 'bool',
            'is_freeleech'     => 'bool',
            'is_double_upload' => 'bool',
            'is_refundable'    => 'bool',
            'can_chat'         => 'bool',
            'can_comment'      => 'bool',
            'can_invite'       => 'bool',
            'can_request'      => 'bool',
            'can_upload'       => 'bool',
            'is_incognito'     => 'bool',
            'autogroup'        => 'bool',
            'system_required'  => 'bool',
            'min_ratio'        => 'decimal:2',
        ];
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the users for the group.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the forum permissions for the group.
     *
     * @return HasMany<ForumPermission, $this>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(ForumPermission::class);
    }
}
