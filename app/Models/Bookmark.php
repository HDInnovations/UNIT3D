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
 * App\Models\Bookmark.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int                             $torrent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Bookmark extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\BookmarkFactory> */
    use HasFactory;

    /**
     * Get the user that owns the bookmark.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => User::SYSTEM_USER_ID,
        ]);
    }

    /**
     * Get the user setting for the user that owns the bookmark.
     *
     * @return BelongsTo<UserSetting, $this>
     */
    public function userSetting(): BelongsTo
    {
        return $this->belongsTo(UserSetting::class, 'user_id', 'user_id');
    }

    /**
     * Get the history of the bookmarked torrent for the user that owns the bookmark.
     *
     * @return BelongsTo<History, $this>
     */
    public function history(): BelongsTo
    {
        return $this->belongsTo(History::class, 'user_id', 'user_id')->whereColumn('bookmarks.torrent_id', '=', 'history.torrent_id');
    }

    /**
     * Get the bookmarked torrent.
     *
     * @return BelongsTo<Torrent, $this>
     */
    public function torrent(): BelongsTo
    {
        return $this->belongsTo(Torrent::class);
    }
}
