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

/**
 * App\Models\Permission.
 *
 * @property int  $id
 * @property int  $forum_id
 * @property int  $group_id
 * @property bool $read_topic
 * @property bool $reply_topic
 * @property bool $start_topic
 */
class ForumPermission extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\ForumPermissionFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    public $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array{read_topic: 'bool', reply_topic: 'bool', start_topic: 'bool'}
     */
    protected function casts(): array
    {
        return [
            'read_topic'  => 'bool',
            'reply_topic' => 'bool',
            'start_topic' => 'bool',
        ];
    }

    /**
     * Get the group associated with the permission.
     *
     * @return BelongsTo<Group, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the forum associated with the permission.
     *
     * @return BelongsTo<Forum, $this>
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }
}
